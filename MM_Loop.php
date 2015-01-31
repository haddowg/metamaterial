<?php
namespace HaddowG\MetaMaterial;

class MM_Loop
{

	public $length   = 0;

	public $parent   = NULL;

	public $current  = -1;

	public $name     = NULL;

	public $type     = false;

    public $group_tag = 'div';

    public $loop_tag = 'div';

	function __construct($name, $length, $type, $limit= NULL)
	{
		$this->name   = $name;
		$this->length = $length;
		$this->type   = $type;
        $this->limit = $limit;
	}

	function the_indexed_name()
	{
		echo $this->get_the_indexed_name();
	}

	function get_the_indexed_name()
	{
		return $this->name . '[' . $this->current . ']';
	}

	function is_first()
	{
		if ( $this->current == 0 ) return TRUE;

		return FALSE;
	}

	function is_last()
	{
		if ( ( $this->current + 1 ) == $this->length ) return TRUE;

		return FALSE;
	}

}