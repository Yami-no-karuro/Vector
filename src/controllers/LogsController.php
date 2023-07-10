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
         * @var int $limit
         * @var int $offset
         * @var int $total
         * @var array $logs
         * Retrive SqlClient and request informations.
         */
        $db = SqlClient::getInstance();
        $limit = (int) $request->query->get('limit', 5);
        $limit = $limit <= 5 ? $limit : 5;
        $offset = (int) $request->query->get('offset', 0);
        $offset = $offset >= 0 ? $offset : 0;
        $total = 0;
        $logs = [];

        /**
         * @var array $results
         * Execute query depending on request data.
         */
        if (null !== ($search = $request->query->get('search'))) {
            $results = $db->getResults("SELECT `ID`, `domain`, `log` 
                FROM `logs` WHERE MATCH(log) AGAINST(?) LIMIT ? OFFSET ?", [
                    ['type' => 's', 'value' => $search],
                    ['type' => 'd', 'value' => $limit],
                    ['type' => 'd', 'value' => $offset]
            ]);
        } else {
            $results = $db->getResults("SELECT `ID`, `domain`, `log` 
                FROM `logs` LIMIT ? OFFSET ?", [
                    ['type' => 'd', 'value' => $limit],
                    ['type' => 'd', 'value' => $offset]
            ]);
        }

        /**
         * @var array $logs
         * @var int $total
         * If the request was a search we consider the amount of entries found as total,
         * in any other case use the table count.
         */
        if ($results['success'] and !empty($results['data'])) {
            if (false === is_array($results['data'][0])) {
                $logs[] = $results['data'];
            } else {
                $logs = $results['data'];
            }
            if (null === $search) {
                $total = $db->getResults("SELECT COUNT(ID) AS `total` FROM `logs`");
                $total = $total['success'] ? (int) $total['data']['total'] : 0;
            } else {
                $total = count($logs);
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
