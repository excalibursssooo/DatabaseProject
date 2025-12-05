<?php
class Blockchain {
    private static $instance = null;
    private $pdo;
    private $difficulty = 2;

    // Private constructor to prevent direct instantiation
    private function __construct($pdo) {
        $this->pdo = $pdo;
        $this->createGenesisBlock();
    }

    // Get singleton instance
    public static function getInstance($pdo = null) {
        if (self::$instance === null) {
            if ($pdo === null) {
                throw new Exception('PDO connection is required for first instantiation');
            }
            self::$instance = new self($pdo);
        }
        return self::$instance;
    }

    // prevent cloning
    private function __clone() {}

    // prevent unserialization
    public function __wakeup() {}

    private function createGenesisBlock() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM blockchain");
        if ($stmt->fetchColumn() == 0) {
            $timestamp = time();
            
            $genesisData = [
                'message' => 'Genesis Block - Car Auction System',
                'timestamp' => $timestamp,
                'type' => 'genesis'
            ];
            $genesisData = json_encode($genesisData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $previousHash = '0';
            $nonce = 0;
            
            do {
                $nonce++;
                $hash = $this->calculateHash($previousHash, $genesisData, $nonce);
            } while (substr($hash, 0, $this->difficulty) !== str_repeat('0', $this->difficulty));
            $stmt = $this->pdo->prepare("
                INSERT INTO blockchain (previous_hash, hash, data, nonce, timestamp)
                VALUES (?, ?, ?, ?, FROM_UNIXTIME(?))
            ");
            
            $stmt->execute([
                $previousHash,
                $hash,
                $genesisData,
                $nonce,
                $timestamp
            ]);
        }
    }

    public function calculateHash($previousHash, $data, $nonce) {
        $dataString = $previousHash . 
                     $data . 
                     (string)(int)$nonce;
        
        return hash('sha256', $dataString);
    }

    // Mine new block 
    public function mineBlock($data, $auto_id = null, $customer_id = null) {
        $previousBlock = $this->getLatestBlock();
        $previousHash = $previousBlock['hash'];
        $timestamp = $data['timestamp'];
        $nonce = 0;
        $data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        do {
            $nonce++;
            $hash = $this->calculateHash($previousHash, $data, $nonce);
        } while (substr($hash, 0, $this->difficulty) !== str_repeat('0', $this->difficulty));
        return $this->addBlock($data, $nonce, $previousHash, $auto_id, $customer_id, $hash, $timestamp);
    }

    private function addBlock($data, $nonce, $previousHash, $auto_id = null, $customer_id = null, $hash = null, $timestamp = null) {
        
        $stmt = $this->pdo->prepare("
            INSERT INTO blockchain (previous_hash, hash, data, nonce, auto_id, customer_id, timestamp)
            VALUES (?, ?, ?, ?, ?, ?, FROM_UNIXTIME(?))
        ");
        
        $stmt->execute([
            $previousHash,
            $hash,
            $data,
            $nonce,
            $auto_id,
            $customer_id,
            $timestamp
        ]);
        
        return $hash;
    }
    // Validate blockchain integrity
    public function isValid() {
        $stmt = $this->pdo->query("SELECT * FROM blockchain ORDER BY block_id");
        $blocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($blocks) === 0) return true;
        
        for ($i = 0; $i < count($blocks); $i++) {
            $block = $blocks[$i];
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return false;
            }
            
            $calculatedHash = $this->calculateHash(
                $block['previous_hash'],
                $block['data'],
               $block['nonce']
            );

            if ($block['hash'] !== $calculatedHash) {
                echo 'Invalid block: ';
                return false;
            }
            
            if (substr($block['hash'], 0, $this->difficulty) !== str_repeat('0', $this->difficulty)) {
                echo 'Block does not meet difficulty: ';
                return false;
            }
            
            if ($i > 0 && $block['previous_hash'] !== $blocks[$i-1]['hash']) {
                echo 'Invalid block linkage: ';
                return false;
            }
        }
        
        return true;
    }
    // Get latest block
    public function getLatestBlock() {
        $stmt = $this->pdo->query("SELECT * FROM blockchain ORDER BY block_id DESC LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Get all blocks
    public function getChain() {
        $stmt = $this->pdo->query("SELECT * FROM blockchain ORDER BY block_id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get blocks by vehicle ID
    public function getBlocksByAutoId($auto_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM blockchain WHERE auto_id = ? ORDER BY block_id");
        $stmt->execute([$auto_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>