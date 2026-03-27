<?php

declare(strict_types=1);

class TestFonctionnel
{
    private string $baseUrl = 'http://php:80';
    private array  $resultats = [];

    public function testAccueil(): void
    {
        $this->resultats['Accueil'] = $this->verifier('/');
    }

    public function testAnnonce(): void
    {
        $this->resultats['Annonce'] = $this->verifier('/item/1');
    }

    private function verifier(string $path): bool
    {
        $headers = @get_headers($this->baseUrl . $path, true);
        if ($headers === false) {
            return false;
        }
        $status = (int) explode(' ', $headers[0])[1];
        return $status === 200;
    }

    public function executer(): void
    {
        $this->testAccueil();
        $this->testAnnonce();

        foreach ($this->resultats as $test => $ok) {
            echo ($ok ? 'V' : 'X') . " $test\n";
        }
    }
}

$test = new TestFonctionnel();
$test->executer();
