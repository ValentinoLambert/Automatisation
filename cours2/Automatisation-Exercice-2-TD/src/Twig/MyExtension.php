<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MyExtension extends AbstractExtension
{
    public function getName(): string
    {
        return 'my-extension';
    }

		public function getFunctions(): array {
			return [
				new TwigFunction('getEnvironmentVariable', [$this, 'getEnvironmentVariable']),
				new TwigFunction('getViteAssets', [$this, 'getViteAssets']),
			];
		}

		public function getEnvironmentVariable(string $varName): ?string {
			return $_ENV[$varName] ?? null;
		}

		public function manifest() {
			$json_file_path = __DIR__ . '/../../public/build/.vite/manifest.json';
			if (file_exists($json_file_path)) {
				$json_data = file_get_contents($json_file_path);

				return json_decode($json_data, true);
			}

			return '';
		}

		public function getViteAssets(): string {
			if ($_ENV['ENV'] === 'prod') {
				$manifest = $this->manifest();
				if (!$manifest) {
					return '';
				}

			$css = $manifest['main.js']['css'][0] ?? '';
			$js = $manifest['main.js']['file'] ?? '';
			
			$html = '';
			if ($css) {
				$html .= '<link rel="stylesheet" href="/build/' . $css . '">';
			}
			if ($js) {
				$html .= '<script type="module" src="/build/' . $js . '"></script>';
			}
			
			return $html;
		}
		else {
			return '<script type="module" src="http://localhost:3000/build/@vite/client"></script>' .
			       '<script type="module" src="http://localhost:3000/build/main.js"></script>';
		}
	}
}
