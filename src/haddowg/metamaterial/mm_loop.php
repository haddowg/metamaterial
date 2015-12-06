<?php
namespace HaddowG\MetaMaterial;

class MM_Loop
{

	public $length   = NULL;

	public $initLength = NULL;

	public $parent   = NULL;

	public $current  = -1;

	public $name     = NULL;

	public $type     = FALSE;

    public $group_tag = 'div';

    public $loop_tag = 'div';

	public $and_one = FALSE;

	function __construct($name, $length, $type, $limit= NULL)
	{
		$this->name   = $name;
		$this->length = $length;
		$this->type   = $type;
        $this->limit = $limit;
		$this->initLength = $length;
		$this->and_one = ($type == 'multi');
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