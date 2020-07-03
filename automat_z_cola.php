<?php
class automat {
    private stdClass $coins;
    private stdClass $returnedCoins;
    private float $colaCost;
    private string $lastError;

    public function __construct() {
        $this->initCoins();
        $this->returnedCoins = new stdClass;
        $this->colaCost = 4.98;
        $this->lastError = "";
    }

    private function initCoins() {
        $this->coins = new stdClass();
        $this->coins->{0.01} = 10;
        $this->coins->{0.02} = 10;
        $this->coins->{0.05} = 0;
        $this->coins->{0.10} = 7;
        $this->coins->{0.20} = 4;
        $this->coins->{0.50} = 20;

        $this->coins->{1} = 13;
        $this->coins->{2} = 3;
        $this->coins->{5} = 5;

        $this->coins->{10} = 4;
        $this->coins->{20} = 8;
        $this->coins->{50} = 2;

        $this->coins->{100} = 1;
        $this->coins->{200} = 1;
    }

    private function validate($value): bool {
        if($value === null) {
            $this->lastError = "Nie podano kwoty";
            return false;
        }

        if(!is_numeric($value)) {
            $this->lastError = "Wprowadzona wartość nie jest liczbą";
            return false;
        }

        if( round($value, 2) < $this->colaCost) {
            $this->lastError = "Wprowadzona kwota nie wystarcza na zakup puszki coli";
            return false;
        }

        return true;
    }

    private function giveTheChange(float $diff): bool {
        if($diff === 0) return true;
        $coinsCopy = clone $this->coins;

        do {
            $coin = 0;
            foreach($coinsCopy as $k => $v) {
                if( bccomp($k, $coin, 2) > 0 && bccomp($k, $diff, 2) <= 0 && ($v > 0) ) {
                    $coin = round($k, 2);
                }
            }

            if($coin === 0) break;

            while(($coinsCopy->$coin !== 0) && bccomp($diff, $coin, 2) >= 0) {
                $diff = floatval(bcsub($diff, $coin, 2));
                $coinsCopy->$coin--;
                if(!property_exists($this->returnedCoins, $coin)) {
                    $this->returnedCoins->$coin = 1;
                } else {
                    $this->returnedCoins->$coin++;
                }
            }

        } while ($diff > 0);

        $result = bccomp($diff, 0, 2) === 0;
        if($result === true) {
            $this->coins = $coinsCopy;
        }

        return $result;
    }

    public function getLastError() {
        return $this->lastError;
    }

    public function __toString() {
        return "Automat zawiera następujące nominały:<br>" .
            "1 gr = {$this->coins->{0.01}}<br>" .
            "2 gr = {$this->coins->{0.02}}<br>" .
            "5 gr = {$this->coins->{0.05}}<br>" .
            "10 gr = {$this->coins->{0.10}}<br>" .
            "20 gr = {$this->coins->{0.20}}<br>" .
            "50 gr = {$this->coins->{0.50}}<br>" .
            "1 zł = {$this->coins->{1}}<br>" .
            "2 zł = {$this->coins->{2}}<br>" .
            "5 zł = {$this->coins->{5}}<br>" .
            "10 zł = {$this->coins->{10}}<br>" .
            "20 zł = {$this->coins->{20}}<br>" .
            "50 zł = {$this->coins->{50}}<br>" .
            "100 zł = {$this->coins->{100}}<br>" .
            "200 zł = {$this->coins->{200}}<br>";
    }

    public function getColaCost() {
        return $this->colaCost;
    }

    public function getReturnCoins() {
        $result = "";
    
        if(property_exists($this->returnedCoins, 0.01)) {
            $result .= "1 gr = {$this->returnedCoins->{0.01}}<br>";
        }
        if(property_exists($this->returnedCoins, 0.02)) {
            $result .= "2 gr = {$this->returnedCoins->{0.02}}<br>";
        }
        if(property_exists($this->returnedCoins, 0.05)) {
            $result .= "5 gr = {$this->returnedCoins->{0.05}}<br>";
        }
        if(property_exists($this->returnedCoins, 0.10)) {
            $result .= "10 gr = {$this->returnedCoins->{0.10}}<br>";
        }
        if(property_exists($this->returnedCoins, 0.20)) {
            $result .= "20 gr = {$this->returnedCoins->{0.20}}<br>";
        }
        if(property_exists($this->returnedCoins, 0.50)) {
            $result .= "50 gr = {$this->returnedCoins->{0.50}}<br>";
        }
        if(property_exists($this->returnedCoins, 1)) {
            $result .= "1 zł = {$this->returnedCoins->{1}}<br>";
        }
        if(property_exists($this->returnedCoins, 2)) {
            $result .= "2 zł = {$this->returnedCoins->{2}}<br>";
        }
        if(property_exists($this->returnedCoins, 5)) {
            $result .= "5 zł = {$this->returnedCoins->{5}}<br>";
        }
        if(property_exists($this->returnedCoins, 10)) {
            $result .= "10 zł = {$this->returnedCoins->{10}}<br>";
        }
        if(property_exists($this->returnedCoins, 20)) {
            $result .= "20 zł = {$this->returnedCoins->{20}}<br>";
        }
        if(property_exists($this->returnedCoins, 50)) {
            $result .= "50 zł = {$this->returnedCoins->{50}}<br>";
        }
        if(property_exists($this->returnedCoins, 100)) {
            $result .= "100 zł = {$this->returnedCoins->{100}}<br>";
        }
        if(property_exists($this->returnedCoins, 200)) {
            $result .= "200 zł = {$this->returnedCoins->{200}}";
        }

        if($result === "") {
            $result = "Automat nie zwrócił pieniędzy<br>";
        } else {
            $result = "Automat zwrócił nominały:<br>" . $result;
        }

        return $result;
    }

    public function buyCola($value) {
        if($this->validate($value) === false) return;
        $diff = floatval(bcsub($value, $this->colaCost, 2));
        if($this->giveTheChange($diff) === false) {
            $this->lastError = "Automat nie mógł zwrócić reszty z kwoty " . $value . " zł";
        }
    }

}
    $at = new automat();

    if(isset($_POST["money"])) {
        $at->buyCola($_POST["money"]);
    }
?>

<html>
<head>
</head>
<body>
    <form action="" method="POST">
        <input type="number" placeholder="Proszę wprowadzić kwotę" step="0.01" value="0" name="money">
        <input type="submit">
    </form>

    <?php if($at->getLastError() !== "") : ?>
        <p style="color:red;">
            <?php echo $at->getLastError(); ?>
        </p>
    <?php elseif(isset($_POST["money"])): ?>
        <p>
            Wpłaciłeś: <?php echo $_POST["money"] ?> zł<br>
            Kupiłeś puszkę coli!<br>
            <?php echo $at->getReturnCoins(); ?>
        </p>
    <?php endif ?>

    <br>
    <p>
        Koszt jednej puszki coli: <?php echo $at->getColaCost(); ?> zł
    </p>

    <p>
        <?php
            echo $at;
        ?>
    </p>
</body>

</html>