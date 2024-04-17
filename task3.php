<?php

require 'vendor/phplucidframe/console-table/src/LucidFrame/Console\ConsoleTable.php';

class KeyGenerator
{
    private $key;

    public function generateKey()
    {
        return $this->key = strtoupper(bin2hex(random_bytes(32)));;
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
        return strtoupper(hash_hmac('sha256', $move, $this->keyGenerator->generateKey()));
    }
}

class Rules
{
    private $moves;

    public function __construct($moves)
    {
        $this->moves = $moves;
    }

    public function determineWinner($userMoves, $computerMoves)
    {
        if(sign(($userMoves - $computerMoves + intdiv(count($this->moves), 2) + count($this->moves))
                % count($this->moves) - intdiv(count($this->moves), 2)) < 0){
            return 'Win';
        }
        else if(sign(($userMoves - $computerMoves + intdiv(count($this->moves), 2) + count($this->moves))
            % count($this->moves) - intdiv(count($this->moves), 2)) > 0){
            return 'Lose';
        }
        else{
            return 'Draw';
        }

    }

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
                $row[] = $this->determineWinner($i, $j);
            }
            $table->addRow($row);
        }

        return $table;
    }
}

class Game
{
    private $moves;
    private $rules;
    private $hmacGenerator;
    private $keyGenerator;

    public function __construct($moves)
    {
        $this->hmacGenerator = new HMAC();
        $this->keyGenerator = new KeyGenerator();
        $this->moves = $moves;
        $this->rules = new Rules($moves);
    }

    public function startGame()
    {
        while(true){
            $computerMoveIndex = mt_rand(1, count($this->moves));
            $computerMove = $this->moves[$computerMoveIndex - 1];
            echo "HMAC:" . $this->hmacGenerator->generateHMAC($computerMove) . "\n";
            echo "Available moves:\n";
            foreach ($this->moves as $key => $value){
                echo $key + 1 . ' - ' . $value . "\n";
            }
            echo "0 - Exit \n";
            echo "? - Help \n";
            echo "Enter your move:\n";
            $userMoveIndex = trim(fgets(STDIN));
            if($userMoveIndex == 0){
                break;
            }
            if($userMoveIndex == '?'){
                echo $this->rules->showRules()->getTable(). "\n" ;
                continue;
            }
            if(!array_key_exists($userMoveIndex - 1, $this->moves)){
                continue;
            }

            $userMove = $this->moves[$userMoveIndex - 1];

            echo "Your move: " . $userMove . "\n";
            echo "Computer move: ". $computerMove . "\n";

            if($this->rules->determineWinner($userMoveIndex, $computerMoveIndex) == 'Win'){
                echo "You Win! \n";
            }
            else if($this->rules->determineWinner($userMoveIndex, $computerMoveIndex) == 'Lose'){
                echo "You Lose! \n";
            }
            else{
                echo "Draw! \n";
            }
            echo "HMAC key: ". $this->keyGenerator->generateKey() . "\n\n";
        }

    }
}

$moves = array_slice($argv, 1);

if(count($moves) < 3){
    echo "Error: Insufficient arguments. You need to pass at least three moves. Example: php script.php rock paper scissors\n";
    exit;
}
if(count($moves) % 2 == 0){
    echo "Error: Incorrect number of arguments. You need to pass an odd number of moves. Example: php script.php rock paper scissors lizard spock\n";
    exit;
}
if(count($moves) !== count(array_unique($moves))){
    echo "Error: Duplicate moves detected. Each move must be unique. Example: php script.php rock paper scissors\n";
    exit;
}

$game = new Game(array_slice($argv,1));
$game->startGame();

function sign($n) {
    return ($n > 0) - ($n < 0);
}