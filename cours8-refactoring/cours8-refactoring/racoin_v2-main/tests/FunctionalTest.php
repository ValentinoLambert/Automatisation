<?php

class TestFonctionnel
{
    private $baseUrl = 'http://php:80';
    private $resultats = [];

    public function testAccueil()
    {
        $this->resultats['Accueil'] = $this->verifier('/');
    }

    public function testAnnonce()
    {
        $this->resultats['Annonce'] = $this->verifier('/item/1');
    }


    private function verifier($path)
    {
        $url = $this->baseUrl . $path;
        $headers = @get_headers($url, 1);
        if ($headers === false) return false;

        $status = (int) explode(' ', $headers[0])[1];
        return $status === 200;
    }

    public function executer()
    {
        $this->testAccueil();
        $this->testAnnonce();

        foreach ($this->resultats as $test => $ok) {
            echo ($ok ? "V" : "X") . " $test\n";

        }
    }
}

$test = new TestFonctionnel();
$test->executer();
