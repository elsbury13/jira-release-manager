<?php

declare(strict_types = 1);

require_once '.env';

class Functions
{
	public function curlRequest(string $data, string $urlType, string $type): ?string
	{
	    $ch = curl_init();

	    curl_setopt_array($ch, [
	        CURLOPT_URL => URL . '/' . $urlType,
	        CURLOPT_USERPWD => USERNAME . ':' . API_KEY,
	        CURLOPT_HTTPHEADER => [
	            'Accept: application/json',
	            'Content-Type: application/json',
	        ],
	        CURLOPT_CUSTOMREQUEST => $type,
	        CURLOPT_POSTFIELDS => $data,
	        CURLOPT_RETURNTRANSFER => true,
	    ]);

	    $release = curl_exec($ch);

	    curl_close ($ch);

	    return $release;
	}

	public function getAll(string $urlType): ?string
	{
	    $ch = curl_init();

	    curl_setopt_array($ch, [
	        CURLOPT_URL => URL . '/' . $urlType,
	        CURLOPT_USERPWD => USERNAME . ':' . API_KEY,
	        CURLOPT_HTTPHEADER => [
	            'Accept: application/json',
	        ],
	        CURLOPT_RETURNTRANSFER => true,
	    ]);

	    $release = curl_exec($ch);

	    curl_close ($ch);

	    return $release;
	}

	public function getReleases(array $projects): array
	{
		$combinedProjects = [];

		foreach ($projects as $project) {
		    $combinedProjects[$project['id']] = $project['name'];
		}

		$releases = [];

		foreach ($combinedProjects as $projectId => $projectName) {
		     $releases[$projectId . '---' . $projectName] = json_decode(
				$this->getAll('project/'. $projectId . '/version?status=unreleased'),
				true
		     );
		}

		$allReleaseInfo = [];

		foreach ($releases as $projectInformation => $release) {
		    $project = explode('---', $projectInformation);
		    foreach ($release as $details) {
		        if (is_array($details)) {
		            foreach ($details as $actualReleases) {
		                $allReleaseInfo[$project[0] . '---' . $actualReleases['id']] = $project[1] . ' - ' . $actualReleases['name'];
		            }
		        }
		    }
		}

		return $allReleaseInfo;
	}
}
