<?php
/******************************************************************************
 *   This file is part of the EventsCoreBundle package.                       *
 *                                                                            *
 *   (c) Events <http://events.cd/>                                           *
 *                                                                            *
 *   For the full copyright and license information, please view the LICENSE  *
 *   file that was distributed with this source code.                         *
 ******************************************************************************/

namespace ZND\USM\UserBundle\Flash;

/**
 * Class UserFlashInfo
 *
 * @package ZND\USM\UserBundle\Flash
 */
interface FlashInterface
{

    /**
     * @param       $messageId
     * @param       $type
     * @param array $params
     *
     * @return bool
     * @internal param $successType
     */
    public function getMessage($messageId,$type, array $params = []);

    /**
     * @param string $messageId
     * @param string $type
     * @param array  $params
     */
    public function addMessage($messageId, $type, array $params=[]);
}