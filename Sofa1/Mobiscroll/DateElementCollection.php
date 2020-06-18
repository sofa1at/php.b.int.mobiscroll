<?php


namespace Sofa1\Mobiscroll;


use Sofa1\Mobiscroll\Models\AbstractDateElement;

class DateElementCollection
{
	/**
	 * @var AbstractDateElement[]
	 */
	private $Items = array();

	public function Add($element)
	{
		$this->Items[] = $element;
	}

	/**
	 * @param AbstractDateElement[] $elements
	 */
	public function AddArray($elements)
	{
		$this->Items = array_merge($this->Items, $elements);
	}

	public function ToString()
	{
		$returnValues = array();
		foreach ($this->Items as $item)
		{
			$returnValues[] = $item->ToString();
		}

		return implode(",", $returnValues);
	}
}
