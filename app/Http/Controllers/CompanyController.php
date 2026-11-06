<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Student;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request){
            $industry = trim((string)$request->query('industry'));
            $company = Company::query()
            ->withCount('students')
            ->where('is_active', true)
            ->when($industry, fn ($q) => $q->where('industry', $industry))
            ->orderBy('id')
            ->paginate(9)
            ->withQueryString();

            $active = Company::where('is_active', true);
            $capacity=(clone $active)->sum('capacity');
            $filled = Student::whereNotNull('company_id')->count();
            $rating =(float) (clone $active)-> avg('rating');

            $stats = [
                'capacity' => $capacity,
                'filled' => $filled,
                'open' => max(0, $capacity - $filled),
                'percent' => $capacity>0 ? min(100, (int) round($filled/$capacity *100)):0,
                'partners'=> (clone $active)->count(),
                'added' => (clone $active)-> where('created_at', '>=', now()->subMonths(6))->count(),
                'rating' => number_format($rating, 1),
                'ratingNote' => match(true){
                    $rating >= 4.5 => 'Excellent network health',
                    $rating >= 4.0 => 'Healthy network',
                    default => 'Needs attention',
                },
            

            ];

            return view('companies.index',[
                'companies' => $company,
                'stats' => $stats,
                'industry' => $industry,
                'industries' => Company::whereNotNull('industry')->distinct()->orderBy('industry')->pluck('industry'),

            ]);



    }
    public function create()
{
    return view('companies.form', [
        'company'    => new Company(['is_active' => true, 'capacity' => 5]),
        'industries' => $this->industries(),
    ]);
}

public function store(Request $request)
{
    Company::create($this->validated($request));

    return redirect()->route('companies.index')->with('status', 'Partner company added.');
}

public function edit(Company $company)
{
    return view('companies.form', [
        'company'    => $company,
        'industries' => $this->industries(),
    ]);
}

public function update(Request $request, Company $company)
{
    $company->update($this->validated($request));

    return redirect()->route('companies.index')->with('status', "{$company->name} was updated.");
}

public function destroy(Company $company)
{
    if ($company->students()->exists()) {
        return back()->with('error', "{$company->name} still has interns placed. Reassign them first, or mark the company inactive instead.");
    }

    $company->delete();

    return redirect()->route('companies.index')->with('status', 'Partner company deleted.');
}

private function industries()
{
    return Company::whereNotNull('industry')->distinct()->orderBy('industry')->pluck('industry');
}

private function validated(Request $request): array
{
    $data = $request->validate([
        'name'            => ['required', 'string', 'max:120'],
        'industry'        => ['nullable', 'string', 'max:80'],
        'capacity'        => ['required', 'integer', 'min:0', 'max:500'],
        'rating'          => ['nullable', 'numeric', 'between:0,5'],
        'supervisor_name' => ['nullable', 'string', 'max:120'],
    ]);

    $data['is_active'] = $request->boolean('is_active');

    return $data;
}
}
