<?php

namespace AppBundle\Repository;

/**
 * PayementFileRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PayementFileRepository extends Repository
{

    public function hashAlreadyExist($hash)
    {
        $result = $this->findOneBy(array('hash'=>$hash));

        if($result == null)
            return false;
        else
            return true;
    }

}