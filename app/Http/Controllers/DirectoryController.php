<?php

namespace App\Http\Controllers;

use App\Models\Fighter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DirectoryController extends Controller
{
    /**
     * Display the directory page with search and filtering
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return $this->getDirectoryResults($request, 'fighters');
    }

    /**
     * Display the professionals directory
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function professionals(Request $request)
    {
        return $this->getDirectoryResults($request, 'professionals');
    }

    /**
     * Display the gyms directory
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function gyms(Request $request)
    {
        return $this->getDirectoryResults($request, 'gyms');
    }

    /**
     * Display a specific fighter profile
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $fighter = Fighter::with(['photos', 'country', 'city'])->findOrFail($id);

        return view('pages.view-fighter-profile', compact('fighter'));
    }

    /**
     * Common method to get directory results with filtering
     *
     * @param Request $request
     * @param string $category
     * @return \Illuminate\View\View
     */
    private function getDirectoryResults(Request $request, $category)
    {
        $query = Fighter::with(['photos', 'country', 'city'])->where('category', $category);

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Filter by gender (only for fighters)
        if ($category === 'fighters' && $request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter by country
        if ($request->filled('country')) {
            $query->where('country_id', $request->country);
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city_id', $request->city);
        }

        // Filter by discipline
        if ($request->filled('discipline')) {
            $query->where('discipline', $request->discipline);
        }

        // Filter by stance (only for fighters)
        if ($category === 'fighters' && $request->filled('stance')) {
            $query->where('stance', $request->stance);
        }

        // Filter by experience (only for fighters)
        if ($category === 'fighters' && $request->filled('experience')) {
            $query->where('experience', $request->experience);
        }

        // Filter by level (only for fighters)
        if ($category === 'fighters' && $request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filter by height range (only for fighters)
        if ($category === 'fighters') {
            if ($request->filled('height_min')) {
                $query->where('height', '>=', $request->height_min);
            }
            if ($request->filled('height_max')) {
                $query->where('height', '<=', $request->height_max);
            }

            // Filter by weight range (only for fighters)
            if ($request->filled('weight_min')) {
                $query->where('weight', '>=', $request->weight_min);
            }
            if ($request->filled('weight_max')) {
                $query->where('weight', '<=', $request->weight_max);
            }
        }

        // Filter by profession (for professionals and gyms)
        if (($category === 'professionals' || $category === 'gyms') && $request->filled('profession')) {
            $query->where('primary_profession', $request->profession);
        }

        // Filter by gym type (for gyms)
        if ($category === 'gyms' && $request->filled('gym_type')) {
            $query->where('gym_type', $request->gym_type);
        }

        // Order by creation date (most recent first)
        $query->orderBy('created_at', 'desc');

        // Get paginated results
        $fighters = $query->paginate(30);

        // Add discipline count for display (simplified - in real app this would be a relationship)
        foreach ($fighters as $fighter) {
            $fighter->discipline_count = 1; // Placeholder - would be dynamic based on relationships
        }

        $pageTitle = ucfirst($category);
        if ($category === 'professionals') {
            $viewName = 'pages.professionals';
        } elseif ($category === 'gyms') {
            $viewName = 'pages.gyms';
        } else {
            $viewName = 'pages.directory';
        }

        return view($viewName, compact('fighters', 'category', 'pageTitle'));
    }
}
