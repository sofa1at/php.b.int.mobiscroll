<?php


namespace Sofa1\Mobiscroll;


use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Exception\SassException;
use Sofa1\Core\Common\Exceptions\StoreException;
use Sofa1\Core\Common\Interfaces\IIndexedStore;

class MobiscrollStylingService
{
	private ?IIndexedStore $store;
	private ?string $cacheKey;

	public function __construct(IIndexedStore $store, $cacheKey)
	{
		if ($store != null)
		{
			$this->store = $store;
		}
		if ($cacheKey != null)
		{
			$this->cacheKey = $cacheKey;
		}
	}

	/**
	 * @param array $colors
	 * [
	 * "mbsc-ios-accent" => $cs->Accent,
	 * "mbsc-ios-background" => $cs->Background,
	 * "mbsc-ios-text" => $cs->Foreground,
	 * "mbsc-ios-dark-accent" => $cs->Accent,
	 * "mbsc-ios-dark-background" => $cs->Background,
	 * "mbsc-ios-dark-text" => $cs->Foreground,
	 * ]
	 *
	 * @throws StoreException|SassException
	 */
	public function GetCSS(array $colors): string
	{
		if ($this->HasStore() && $this->store->HasValue($this->cacheKey))
		{
			return $this->store->Read($this->cacheKey);
		}
		else
		{
			$compiler = new Compiler();
			$compiler->setOutputStyle(\ScssPhp\ScssPhp\OutputStyle::COMPRESSED);
			if ( ! empty($colors))
			{
				$compiler->addVariables($colors);
			}
			$compiler->setImportPaths(__DIR__ . "/../assets/");
			$result = $compiler->compileString('@import "css/mobiscroll.javascript.min.scss"');

			$css = $result->getCss();
			if ($this->HasStore())
			{
				$this->store->Save($this->cacheKey, $css);
			}

			return $css;
		}
	}

	private function HasStore(): bool
	{
		return $this->store != null && $this->cacheKey != null;
	}
}
