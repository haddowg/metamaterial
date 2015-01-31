<?php
namespace HaddowG\MetaMaterial;

/**
 * @package     MetaMaterial
 * @author      Gregory Haddow
 * @copyright   Copyright (c) 2014, Gregory Haddow, http://www.greghaddow.co.uk/
 * @license     http://opensource.org/licenses/gpl-3.0.html The GPL-3 License with additional attribution clause as detailed below.
 * @version     0.1
 * @link        http://www.greghaddow.co.uk/MetaMaterial
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program has the following attribution requirement (GPL Section 7):
 *     - you agree to retain in MetaMaterial and any modifications to MetaMaterial the copyright, author attribution and
 *       URL information as provided in this notice and repeated in the licence.txt document provided with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

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