<?php
/**
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5
 */
class PDFGenerator extends PDFGeneratorCore
{
	/**
	 * Render the pdf file
	 *
	 * @param string $filename
         * @param  $display :  true:display to user, false:save, 'I','D','S' as fpdf display
	 * @throws PrestaShopException
	 */
	public function render($filename, $display = true)
	{
		if (empty($filename))
			throw new PrestaShopException('Missing filename.');

		$this->lastPage();

		if ($display === true)
			$output = 'D';
		elseif ($display === false)
			$output = 'S';
		elseif ($display == 'D')
			$output = 'D';
		elseif ($display == 'S')
			$output = 'S';
		elseif ($display == 'F')
			$output = 'F';
		elseif ($display == 'E')
			$output = 'E';
		else 	
			$output = 'I';
			
		return $this->output($filename, $output);
	}
}
