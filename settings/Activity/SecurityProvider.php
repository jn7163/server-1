<?php

/**
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 * @copyright Copyright (c) 2016 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * Two-factor backup codes
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OC\Settings\Activity;

use InvalidArgumentException;
use OCP\Activity\IEvent;
use OCP\Activity\IManager;
use OCP\Activity\IProvider;
use OCP\IURLGenerator;
use OCP\L10N\IFactory as L10nFactory;

class SecurityProvider implements IProvider {

	/** @var L10nFactory */
	private $l10n;

	/** @var IURLGenerator */
	private $urlGenerator;

	/** @var IManager */
	private $activityManager;

	public function __construct(L10nFactory $l10n, IURLGenerator $urlGenerator, IManager $activityManager) {
		$this->urlGenerator = $urlGenerator;
		$this->l10n = $l10n;
		$this->activityManager = $activityManager;
	}

	public function parse($language, IEvent $event, IEvent $previousEvent = null) {
		if ($event->getType() !== 'security') {
			throw new InvalidArgumentException();
		}

		$l = $this->l10n->get('core', $language);

		switch ($event->getSubject()) {
			case 'twofactor_success':
				$params = $event->getSubjectParameters();
				$event->setParsedSubject($l->t('You successfully logged in using two-factor authentication (%1$s)', [
							$params['provider'],
					]));
				if ($this->activityManager->getRequirePNG()) {
					$event->setIcon($this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath('core', 'actions/password.png')));
				} else {
					$event->setIcon($this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath('core', 'actions/password.svg')));
				}
				break;
			case 'twofactor_failed':
				$params = $event->getSubjectParameters();
				$event->setParsedSubject($l->t('A login attempt using two-factor authentication failed (%1$s)', [
							$params['provider'],
					]));
				if ($this->activityManager->getRequirePNG()) {
					$event->setIcon($this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath('core', 'actions/password.png')));
				} else {
					$event->setIcon($this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath('core', 'actions/password.svg')));
				}
				break;
			default:
				throw new InvalidArgumentException();
		}
		return $event;
	}

}
