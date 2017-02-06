<?php
namespace FalkRoeder\DatedNews\Utility;

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * This class provides misc functions for the dated News extension.
 *
 * @author Falk Röder <mail@falk-roeder.de>
 */
class Div {

	/**
	 * configurationManager
	 *
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * objectManager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Generate and send Email
	 *
	 * @param \string Template file in Templates/Email/
	 * @param \array $receiver Combination of Email => Name
	 * @param \array $receiverCc Combination of Email => Name
	 * @param \array $receiverBcc Combination of Email => Name
	 * @param \array $sender Combination of Email => Name
	 * @param \string $subject Mail subject
	 * @param \array $variables Variables for assignMultiple
	 * @return \bool Mail was sent?
	 */
	public function sendEmail($template, $receiver, $receiverCc, $receiverBcc, $sender, $subject, $variables = array(), $fileNames) {

		/** @var $emailBodyObject \TYPO3\CMS\Fluid\View\StandaloneView */
		$emailBodyObject = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$emailBodyObject->setTemplatePathAndFilename($this->getTemplatePath('Email/' . $template . '.html'));
		$emailBodyObject->setLayoutRootPaths(array(
				'default' => ExtensionManagementUtility::extPath('dated_news') . 'Resources/Private/Layouts'
		));
		$emailBodyObject->setPartialRootPaths(array(
				'default' => ExtensionManagementUtility::extPath('dated_news') . 'Resources/Private/Partials'
		));
		$emailBodyObject->assignMultiple($variables);

		$email = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
		$email
				->setTo($receiver)
				->setCc($receiverCc)
				->setBcc($receiverBcc)
				->setFrom($sender)
				->setSubject($subject)
				->setCharset($GLOBALS['TSFE']->metaCharset)
				->setBody($emailBodyObject->render(), 'text/html');

		if ($fileNames && is_array($fileNames)) {
			foreach ($fileNames as $fileName){
				if(trim($fileName) != '') {
					$email->attach(\Swift_Attachment::fromPath('fileadmin'. $fileName));
				}
			}
		}

		$email->send();

		return $email->isSent();
	}


	/**
	 * Return path and filename for a file
	 * 		respect *RootPaths and *RootPath
	 *
	 *@todo Remove this function as soon as StandaloneView supports templaterootpaths ... , maybe TYPO3 6.3 ?
	 *
	 * @param string $relativePathAndFilename e.g. Email/Name.html
	 * @return string
	 */
	public function getTemplatePath($relativePathAndFilename) {
		$extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(
				ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
				);
		if (!empty($extbaseFrameworkConfiguration['view']['templateRootPaths'])) {
			foreach ($extbaseFrameworkConfiguration['view']['templateRootPaths'] as $path) {
				$absolutePath = GeneralUtility::getFileAbsFileName($path);
				if (file_exists($absolutePath . $relativePathAndFilename)) {
					$absolutePathAndFilename = $absolutePath . $relativePathAndFilename;
				}
			}
		}
		if (empty($absolutePathAndFilename)) {
			$absolutePathAndFilename = GeneralUtility::getFileAbsFileName(
					'EXT:dated_news/Resources/Private/Templates/' . $relativePathAndFilename
					);
		}
		return $absolutePathAndFilename;
	}
}