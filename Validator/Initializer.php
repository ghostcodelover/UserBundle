<?php

/******************************************************************************
 *   This file is part of the EventsCoreBundle package.                       *
 *                                                                            *
 *   (c) Events <http://events.cd/>                                           *
 *                                                                            *
 *   For the full copyright and license information, please view the LICENSE  *
 *   file that was distributed with this source code.                         *
 ******************************************************************************/

namespace ZND\USM\UserBundle\Validator;

use ZND\USM\UserBundle\Entity\UserInterface;
use ZND\USM\UserBundle\Util\CanonicalFieldsUpdater;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Validator\ObjectInitializerInterface;

/**
 * Automatically updates the canonical fields before validation.
 *
 * @author Muekendi emmanuel <mukendiemmanuel@events.cd>
 *
 * @DI\Service("znd_usm_user.validator.initializer", public=false)
 * @DI\Tag("validator.initializer")
 */
class Initializer implements ObjectInitializerInterface
{
    private $canonicalFieldsUpdater;

    /**
     * Initializer constructor.
     *
     * @param \ZND\USM\UserBundle\Util\CanonicalFieldsUpdater $canonicalFieldsUpdater
     * @DI\InjectParams({
     *     "canonicalFieldsUpdater" = @DI\Inject("znd_usm_user.user_util_canonical_fields_updater")
     * })
     */
    public function __construct(CanonicalFieldsUpdater $canonicalFieldsUpdater)
    {
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    /**
     * @param object $object
     */
    public function initialize($object)
    {
        if ($object instanceof UserInterface) {
            $this->canonicalFieldsUpdater->updateCanonicalFields($object);
        }
    }
}
