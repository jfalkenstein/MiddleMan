<?php
namespace PB\Modules\Demo\walkthrough\interfaces;
/**
 * This interface determines the methods you want your repository to have.
 * In this case, we're just going to need one: getByPersonKey()
 *
 * @author jfalkenstein
 */

interface IRepository {
    /**
     * You should provide some comments on what this function is intended to do
     * and return.
     * @param int $personkey The person key of the person you want to find.
     */
    public function getByPersonKey($personkey);
}
