<?php

require 'vendor/phplucidframe/console-table/src/LucidFrame/Console\ConsoleTable.php';

class KeyGenerator
{
    private $key;

    public function __construct()
    {
        $this->key = random_bytes(32);
    }

    public function getKey()
    {
        return $this->key;
    }
}
class HMAC
{
    private $keyGenerator;

    public function __construct()
    {
        $this->keyGenerator = new KeyGenerator();
    }

    public function generateHMAC($move)
    {
        return hash_hmac('sha256', $move, $this->keyGenerator->getKey());
    }
}

class Rules
{
    private $moves = ['Rock', 'Scissors', 'Paper'];

    public function showRules()
    {
        $movesCount = count($this->moves);

        $table = new \LucidFrame\Console\ConsoleTable();
        $table->addHeader('v PC/ USER >');

        foreach ($this->moves as $moveHeader){
            $table->addHeader($moveHeader);
        }

        for($i = 0; $i<$movesCount; $i++){
            $row = [];
            $row[] = $this->moves[$i];
            for($j = 0; $j < $movesCount; $j++){
                if(sign(($i - $j + intdiv($movesCount, 2) + $movesCount)
                    % $movesCount - intdiv($movesCount, 2)) > 0){
                    $row[] = 'Win';
                }
                else if(sign(($i - $j + intdiv($movesCount, 2) + $movesCount)
                        % $movesCount - intdiv($movesCount, 2)) < 0){
                    $row[] = 'Lose';
                }
                else{
                    $row[] = 'Draw';
                }
            }
            $table->addRow($row);
        }

        return $table;
    }
}


$rules = new Rules();

echo $rules->showRules()->getTable();

function sign($n) {
    return ($n > 0) - ($n < 0);
}