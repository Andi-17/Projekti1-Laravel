<?php

namespace App\Traits;

use App\Models\Company;

trait ChecksCompanyAccess
{
    public function checkCompanyAccess($company_id)
    {
        $company = Company::findOrFail($company_id);

        if (
            auth()->user()->role->name !== 'admin' &&
            $company->owner_id !== auth()->id()
        ) {
            abort(403, 'Forbidden');
        }

        return $company;
    }
}