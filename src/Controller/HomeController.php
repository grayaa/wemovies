<?php

namespace App\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $pageNumber = $request->query->getInt('page', 1); // Get the page number from the request, default to 1
        $itemsPerPage = 9;
        $client = new Client();
        $options = [
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIwM2U3ZDMxMjcyMDQwZDg1MDlkNjU5Mjg0MDkyNjFiNSIsInN1YiI6IjY0OTYwMGUwOTc2YTIzMDBlNGE2MjBmZCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.O1gyd7h_Gr_ETsbGPxY0cwJWbUD1ga0KJTbkOjgv-bc',
                'accept' => 'application/json',
            ],
            'verify' => false,
            'query' => [
                'page' => $pageNumber,
                'language' => 'en-US',
                'append_to_response' => 'videos',
                'total_results' => 6,
            ],
        ];

        $nowPlaying = $client->request('GET',
            'https://api.themoviedb.org/3/movie/now_playing', $options);
        $nowPlaying = json_decode($nowPlaying->getBody(), true)["results"];

        $trending = $client->request('GET',
            'https://api.themoviedb.org/3/trending/movie/day', $options);

        $genresResponse = $client->request('GET',
            'https://api.themoviedb.org/3/genre/movie/list', $options);

        $genres = json_decode($genresResponse->getBody(), true)["genres"];

        $trendingMovies = json_decode($trending->getBody(), true)["results"];
        foreach ($trendingMovies as &$movie){
            $movieDetails = $client->request('GET',
                'https://api.themoviedb.org/3/movie/'.$movie["id"].'?api_key=03e7d31272040d8509d65928409261b5', $options);
            $movieDetails = json_decode($movieDetails->getBody()->getContents(), true);
            $movie = array_merge($movie, $movieDetails);
        }

        foreach ($nowPlaying as &$movie){
            $movieDetails = $client->request('GET',
                'https://api.themoviedb.org/3/movie/'.$movie["id"].'?api_key=03e7d31272040d8509d65928409261b5', $options);
            $movieDetails = json_decode($movieDetails->getBody()->getContents(), true);
            $movie = array_merge($movie, $movieDetails);
        }

        $pagination = $paginator->paginate(
            $nowPlaying,
            $pageNumber,
            $itemsPerPage
        );

        return $this->render('home/index.html.twig', [
            'movies' => $nowPlaying,
            'trendingMovies' => $trendingMovies,
            'trendingMovie' => $trendingMovies[0],
            'genres' => $genres,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    #[Route('/ajax_get_by_genres', name: 'get_by_genres')]
    public function getByGenres(Request $request, PaginatorInterface $paginator): Response
    {
        $pageNumber = $request->query->getInt('page', 1); // Get the page number from the request, default to 1
        $itemsPerPage = 9;
        $client = new Client();
        $genresIds = $request->request->get('genres');
        $options = [
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIwM2U3ZDMxMjcyMDQwZDg1MDlkNjU5Mjg0MDkyNjFiNSIsInN1YiI6IjY0OTYwMGUwOTc2YTIzMDBlNGE2MjBmZCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.O1gyd7h_Gr_ETsbGPxY0cwJWbUD1ga0KJTbkOjgv-bc',
                'accept' => 'application/json',
            ],
            'verify' => false,
            'query' => [
                'page' => $pageNumber,
                'language' => 'en-US',
                'append_to_response' => 'videos',
                'with_genres' => implode('|', $genresIds)
            ],
        ];

        $url = 'https://api.themoviedb.org/3/discover/movie';
        $moviesByGenres = $client->request('GET',
            $url, $options);

        $moviesByGenres = json_decode($moviesByGenres->getBody(), true)["results"];

        foreach ($moviesByGenres as &$movie){
            $movieDetails = $client->request('GET',
                'https://api.themoviedb.org/3/movie/'.$movie["id"].'?api_key=03e7d31272040d8509d65928409261b5', $options);
            $movieDetails = json_decode($movieDetails->getBody()->getContents(), true);
            $movie = array_merge($movie, $movieDetails);
        }

        $pagination = $paginator->paginate(
            $moviesByGenres,
            $pageNumber,
            $itemsPerPage
        );

        return $this->render("includes/movies.html.twig",[
            "movies" => $moviesByGenres,
            'pagination' => $pagination,
        ]);


    }

    #[Route('/ajax_autocomplete', name: 'autocomplete')]
    public function autocomplete(Request $request): JsonResponse
    {
        $query = $request->query->get('query');

        $client = new Client();

        $options = [
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIwM2U3ZDMxMjcyMDQwZDg1MDlkNjU5Mjg0MDkyNjFiNSIsInN1YiI6IjY0OTYwMGUwOTc2YTIzMDBlNGE2MjBmZCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.O1gyd7h_Gr_ETsbGPxY0cwJWbUD1ga0KJTbkOjgv-bc',
                'accept' => 'application/json',
            ],
            'verify' => false,
            'query' => [
                'query' => $query,
                'page' => 1,
                'language' => 'en-US',
                'append_to_response' => 'videos'
            ],
        ];
        $response = $client->request('GET', 'https://api.themoviedb.org/3/search/movie', $options);

        $data = json_decode($response->getBody(), true);

        $results = $data['results'] ?? [];

        $movies = [];

        foreach ($results as $result) {
            $movies[] = [
                'id' => $result['id'],
                'title' => $result['title'],
                'image' => $result['poster_path'],
            ];
        }

        return new JsonResponse(['results' => $movies]);
    }

    #[Route('/ajax_load_movie_modal', name: 'load_movie_modal')]
    public function loadMovieModal(Request $request): Response
    {
        $id = $request->request->get("id");
        $movieDetails = $this->getMovieById($id);
        return $this->render("includes/movies-modal.html.twig",[
            "movie" => $movieDetails
        ]);
    }

    /**
     * @Route("/movie_details/{id}", name="movie_details")
     * @throws GuzzleException
     */
    public function movieDetails(Request $request, $id): Response
    {
        $movieDetails = $this->getMovieById($id);

        return $this->render("movies-details.html.twig", [
            "movie" => $movieDetails
        ]);
    }


    /**
     * @param $id
     * @return mixed
     * @throws GuzzleException
     */
    public function getMovieById($id): mixed
    {
        $client = new Client();

        $options = [
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIwM2U3ZDMxMjcyMDQwZDg1MDlkNjU5Mjg0MDkyNjFiNSIsInN1YiI6IjY0OTYwMGUwOTc2YTIzMDBlNGE2MjBmZCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.O1gyd7h_Gr_ETsbGPxY0cwJWbUD1ga0KJTbkOjgv-bc',
                'accept' => 'application/json',
            ],
            'verify' => false,
            'query' => [
                'page' => 1,
                'language' => 'en-US',
                'append_to_response' => 'videos'
            ],
        ];
        $movieDetails = $client->request('GET',
            'https://api.themoviedb.org/3/movie/' . $id . '?api_key=03e7d31272040d8509d65928409261b5', $options);
        $movieDetails = json_decode($movieDetails->getBody()->getContents(), true);
        return $movieDetails;
    }

}
