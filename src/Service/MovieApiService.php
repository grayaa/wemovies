<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class MovieApiService
{
    private const NOW_PLAYING_MOVIES_ENDPOINT = 'movie/now_playing';
    private const TRENDING_MOVIES_ENDPOINT = 'trending/movie/day';
    private const GENRE_LIST_ENDPOINT = 'genre/movie/list';
    private const DISCOVER_MOVIE_ENDPOINT = 'discover/movie';
    private const SEARCH_MOVIE_ENDPOINT = 'search/movie';
    private const MOVIE_DETAILS = 'movie/';

    private string $token;
    private Client $client;

    public function __construct(string $token)
    {
        $this->token = $token;
        $this->client = new Client([
            'base_uri' => 'https://api.themoviedb.org/3/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ],
            'verify' => false,
        ]);
    }

    /**
     * Get the now playing movies.
     *
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws GuzzleException
     */
    public function getNowPlayingMovies(int $page = 1, int $perPage = 20): array
    {
        $response = $this->client->get(self::NOW_PLAYING_MOVIES_ENDPOINT, [
            'query' => [
                'page' => $page,
                'language' => 'en-US',
                'append_to_response' => 'videos',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['results'] ?? [];
    }

    /**
     * Get the trending movies.
     *
     * @return array
     * @throws GuzzleException
     */
    public function getTrendingMovies(): array
    {
        $response = $this->client->get(self::TRENDING_MOVIES_ENDPOINT,
            [
                'query' => [
                    'page' => 1,
                    'language' => 'en-US',
                    'append_to_response' => 'videos',
                ],
            ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['results'] ?? [];
    }

    /**
     * Get the list of genres.
     *
     * @return array
     * @throws GuzzleException
     */
    public function getGenres(): array
    {
        $response = $this->client->get(self::GENRE_LIST_ENDPOINT);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['genres'] ?? [];
    }

    /**
     * Get movies by genres.
     *
     * @param array $genreIds
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws GuzzleException
     */
    public function getMoviesByGenres(array $genreIds, int $page = 1, int $perPage = 20): array
    {
        $response = $this->client->get(self::DISCOVER_MOVIE_ENDPOINT, [
            'query' => [
                'page' => $page,
                'language' => 'en-US',
                'append_to_response' => 'videos',
                'with_genres' => implode('|', $genreIds),
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['results'] ?? [];
    }

    /**
     * Search movies by query.
     *
     * @param string $query
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws GuzzleException
     */
    public function searchMovies(string $query, int $page = 1, int $perPage = 20): array
    {
        $response = $this->client->get(self::SEARCH_MOVIE_ENDPOINT, [
            'query' => [
                'query' => $query,
                'page' => $page,
                'language' => 'en-US',
                'append_to_response' => 'videos',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['results'] ?? [];
    }

    /**
     * Get movie details by ID.
     *
     * @param int $movieId
     * @return array|null
     * @throws GuzzleException
     */
    public function getMovieDetails(int $movieId): ?array
    {
        $response = $this->client->get(self::MOVIE_DETAILS . $movieId, [
            'query' => [
                'language' => 'en-US',
                'append_to_response' => 'videos',
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @throws GuzzleException
     */
    public function appendVideos(array $movies): array
    {
        foreach ($movies as &$movie)
        {
            $movie = $this->getMovieDetails($movie["id"]);
        }

        return $movies;
    }
}
