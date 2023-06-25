<?php

namespace App\Controller;

use App\Service\MovieApiService;
use GuzzleHttp\Exception\GuzzleException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private MovieApiService $movieApiService;

    public function __construct(MovieApiService $movieApiService)
    {
        $this->movieApiService = $movieApiService;
    }

    /**
     * @throws GuzzleException
     */
    #[Route('/', name: 'app_home')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $pageNumber = $request->query->getInt('page', 1);
        $itemsPerPage = 9;

        $nowPlaying = $this->movieApiService->getNowPlayingMovies($pageNumber);
        foreach ($nowPlaying as &$movie)
        {
            $movie = $this->movieApiService->getMovieDetails($movie["id"]);
        }
        $trendingMovies = $this->movieApiService->getTrendingMovies();
        $trendingMovies = $this->movieApiService->appendVideos($trendingMovies);
        $genres = $this->movieApiService->getGenres();

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
        $pageNumber = $request->query->getInt('page', 1);
        $itemsPerPage = 9;

        $genresIds = $request->request->get('genres');
        $moviesByGenres = $this->movieApiService->getMoviesByGenres((array)$genresIds, $pageNumber);

        $moviesByGenres = $this->movieApiService->appendVideos($moviesByGenres);

        $pagination = $paginator->paginate(
            $moviesByGenres,
            $pageNumber,
            $itemsPerPage
        );

        return $this->render("includes/movies.html.twig", [
            "movies" => $moviesByGenres,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    #[Route('/ajax_autocomplete', name: 'autocomplete')]
    public function autocomplete(Request $request): JsonResponse
    {
        $query = $request->query->get('query');
        $results = $this->movieApiService->searchMovies($query);

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
        $movieDetails = $this->movieApiService->getMovieDetails($id);

        return $this->render("includes/movies-modal.html.twig", [
            "movie" => $movieDetails
        ]);
    }

    #[Route('/movie_details/{id}', name: 'movie_details')]
    public function movieDetails(Request $request, $id): Response
    {
        $movieDetails = $this->movieApiService->getMovieDetails($id);

        return $this->render("movies-details.html.twig", [
            "movie" => $movieDetails
        ]);
    }
}
