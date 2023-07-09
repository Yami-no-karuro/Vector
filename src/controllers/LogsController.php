<?php

namespace Vector\Controller;

use Vector\Router;
use Vector\Module\Controller\FrontendController;
use Vector\Module\SqlClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class LogsController extends FrontendController
{
    protected function register(): void
    {
        Router::route(['GET'], '^/api/v1/logs?$', [$this, 'logsAction']);
    }

    /**
     * Route: '/api/v1/logs'
     * Methods: GET
     * @return Response
     */
    public function logsAction(Request $request): JsonResponse
    {

        /**
         * @var SqlClient $sql
         * @var int $total
         * Gets the SqlClient and count the entries on database.
         * Total is necessary to handle server side pagination.
         */
        $db = SqlClient::getInstance();
        $logs = [];
        $total = $db->getResults("SELECT COUNT(ID) AS `total` FROM `logs`");
        $total = $total['success'] ? (int) $total['data']['total'] : 0;
        if ($total > 0) {

            /**
             * @var int $limit
             * @var int $offset
             * @var array $logs
             * Looks for limit and offset and retrive current page data.
             */
            $limit = (int) $request->query->get('limit', 5);
            $limit = $limit <= 5 ? $limit : 5;
            $offset = (int) $request->query->get('offset', 0);
            $offset = $offset >= 0 ? $offset : 0;
            $results = $db->getResults("SELECT `ID`, `domain`, `log` 
                FROM `logs` LIMIT ? OFFSET ?", [
                    ['type' => 'd', 'value' => $limit],
                    ['type' => 'd', 'value' => $offset]
            ]);
            if ($results['success'] and !empty($results['data'])) {
                if (false === is_array($results['data'][0])) {
                    $logs[] = $results['data'];
                } else {
                    $logs = $results['data'];
                }
            }
        }

        return new JsonResponse([
            'success' => true,
            'data' => [
                'total' => $total,
                'entries' => $logs
            ]
        ], Response::HTTP_OK);
    }

}
