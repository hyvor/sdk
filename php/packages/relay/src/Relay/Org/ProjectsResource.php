<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Org;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Relay\Dto\ProjectUser;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->org->projects`
 */
final class ProjectsResource
{
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * POST /api/console/project
     *
     * @param array{
     *     name: string,
     *     send_type: 'transactional'|'distributional',
     * } $data
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): ProjectUser
    {
        $result = $this->transport->request('POST', '/api/console/project', $data, $options);

        return $this->transport->denormalize($result, ProjectUser::class);
    }
}
