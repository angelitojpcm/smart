<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\CommandTrait;
use App\Models\Tenant\{
    Company,
};
use App\Services\PseServiceDispatchTask;
use App\Services\PseServiceTask;
use Illuminate\Support\Facades\DB;

class PseDispatchCheckStatus extends Command
{
    use CommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pse:dispatchcheck';
    protected $company = null;
    protected $token = null;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check status of PSE documents';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    function get_documents($state_type_id)
    {
        try {
            $documents = DB::connection('tenant')->table('dispatches')
                ->where('date_of_issue', '>=', '2023-12-01')
                ->where('state_type_id', $state_type_id)
                ->get();

            return $documents;
        } catch (\Exception $e) {
        }
    }
    public function handle()

    {
        $company = Company::firstOrFail();
        $this->company = $company;
        if ($company->pse && $company->pse_url && $company->pse_token && $company->number ) {
            // if($company->number == "20604665966"){

            // $to_send = $this->get_documents('01');
            // $to_validate = $this->get_documents('03');
            // $to_anulate = $this->get_documents('13');

            foreach (['03'] as $state_type_id) {
                $documents = $this->get_documents($state_type_id);
                if ($documents->count() > 0) {
                    new PseServiceDispatchTask($documents, $state_type_id);
                }
            }
        }


        $this->info('The command is finishedq');
    }
}
