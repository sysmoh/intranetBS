<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 15.11.15
 * Time: 16:47
 */

namespace AppBundle\Utils\ListUtils;

/**
 * List all the possible list stored in session
 *
 * Class ListKey
 * @package AppBundle\Utils\ListUtils
 */
class ListKey
{
    const CREANCES_SEARCH_RESULTS = "creances_search_results";
    const FACTURES_SEARCH_RESULTS = "factures_search_results";
    const PAYEMENTS_SEARCH_RESULTS = "payement_search_results";
    const MEMBRES_SEARCH_RESULTS = "membres_search_results";
    const FAMILLE_SEARCH_RESULTS = "famille_search_results";
    const FAMILLE_SEARCH_RESULTS_ADD_MEMBRE = "famille_search_results_add_membre";
}