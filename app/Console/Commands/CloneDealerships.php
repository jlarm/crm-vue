<?php

namespace App\Console\Commands;

use App\Domains\Dealership\Models\Dealership;
use App\Enums\DevStatus;
use App\Enums\Rating;
use App\Enums\Status;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CloneDealerships extends Command
{
    protected $signature = 'dealerships:clone 
                            {source_table : Source table name}
                            {--connection=default : Source database connection}
                            {--truncate : Truncate target table before cloning}
                            {--dry-run : Show what would be cloned without actually doing it}';

    protected $description = 'Clone dealership data from another table, matching only existing fields';

    public function handle()
    {
        $sourceTable = $this->argument('source_table');
        $sourceConnection = $this->option('connection');
        $dryRun = $this->option('dry-run');

        $this->info("Starting dealership clone from {$sourceTable}...");

        if ($this->option('truncate') && !$dryRun) {
            if ($this->confirm('This will delete all existing dealerships. Continue?')) {
                Dealership::truncate();
                $this->info('Dealerships table truncated.');
            } else {
                $this->info('Operation cancelled.');
                return;
            }
        }

        try {
            $validFields = $this->getMatchingFields($sourceTable, $sourceConnection);
            
            if (empty($validFields)) {
                $this->error('No matching fields found between source and target tables.');
                return;
            }

            $this->info('Matching fields: ' . implode(', ', $validFields));

            $totalRecords = DB::connection($sourceConnection)
                ->table($sourceTable)
                ->count();

            $this->info("Found {$totalRecords} records to process.");

            if ($dryRun) {
                $this->showSampleData($sourceTable, $sourceConnection, $validFields);
                return;
            }

            $this->cloneDealerships($sourceTable, $sourceConnection, $validFields, $totalRecords);

        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }

    private function getMatchingFields(string $sourceTable, string $sourceConnection): array
    {
        $targetFields = [
            'name', 'address', 'city', 'state', 'zip_code', 
            'phone', 'current_solution_name', 'current_solution_use',
            'notes', 'status', 'rating', 'type', 'in_development', 'dev_status'
        ];

        $sourceColumns = Schema::connection($sourceConnection)
            ->getColumnListing($sourceTable);

        return array_intersect($targetFields, $sourceColumns);
    }

    private function showSampleData(string $sourceTable, string $sourceConnection, array $fields): void
    {
        $sample = DB::connection($sourceConnection)
            ->table($sourceTable)
            ->select($fields)
            ->limit(3)
            ->get();

        $this->info('Sample data preview:');
        $this->table($fields, $sample->map(fn($row) => (array) $row)->toArray());
    }

    private function cloneDealerships(string $sourceTable, string $sourceConnection, array $fields, int $total): void
    {
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $imported = 0;
        $errors = 0;

        DB::connection($sourceConnection)
            ->table($sourceTable)
            ->select($fields)
            ->orderBy('id')
            ->chunk(100, function ($dealerships) use (&$imported, &$errors, $bar) {
                foreach ($dealerships as $dealership) {
                    try {
                        $data = $this->mapFieldsForTarget((array) $dealership);
                        Dealership::create($data);
                        $imported++;
                    } catch (\Exception $e) {
                        $errors++;
                        $this->newLine();
                        $this->error("Failed to import record: {$e->getMessage()}");
                    }
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();
        $this->info("Successfully imported {$imported} dealerships.");
        
        if ($errors > 0) {
            $this->warn("Failed to import {$errors} records due to errors.");
        }
    }

    private function mapFieldsForTarget(array $data): array
    {
        // Map status to enum
        if (isset($data['status'])) {
            $data['status'] = $this->mapToStatusEnum($data['status']);
        } else {
            $data['status'] = Status::IMPORTED->value;
        }

        // Map rating to enum
        if (isset($data['rating'])) {
            $data['rating'] = $this->mapToRatingEnum($data['rating']);
        }

        // Map dev_status to enum
        if (isset($data['dev_status'])) {
            $data['dev_status'] = $this->mapToDevStatusEnum($data['dev_status']);
        } else {
            $data['dev_status'] = DevStatus::NOT_STARTED->value;
        }

        // Ensure boolean conversion for in_development
        if (isset($data['in_development'])) {
            $data['in_development'] = (bool) $data['in_development'];
        } else {
            $data['in_development'] = false;
        }

        // Set default type if missing
        if (!isset($data['type']) || empty($data['type'])) {
            $data['type'] = 'dealership';
        }

        return $data;
    }

    private function mapToStatusEnum(mixed $value): string
    {
        $value = strtolower(trim($value ?? ''));
        
        return match ($value) {
            'active', '1', 'true', 'yes' => Status::ACTIVE->value,
            'inactive', '0', 'false', 'no' => Status::INACTIVE->value,
            default => Status::IMPORTED->value,
        };
    }

    private function mapToRatingEnum(mixed $value): string
    {
        $value = strtolower(trim($value ?? ''));
        
        return match ($value) {
            'hot', 'high', 'h' => Rating::HOT->value,
            'warm', 'medium', 'w', 'med' => Rating::WARM->value,
            'cold', 'low', 'c' => Rating::COLD->value,
            default => Rating::WARM->value,
        };
    }

    private function mapToDevStatusEnum(mixed $value): string
    {
        $value = strtolower(trim($value ?? ''));
        
        return match ($value) {
            'not_started', 'not started', 'pending', 'new' => DevStatus::NOT_STARTED->value,
            'in_progress', 'in progress', 'active', 'working' => DevStatus::IN_PROGRESS->value,
            'completed', 'complete', 'done', 'finished' => DevStatus::COMPLETED->value,
            'on_hold', 'on hold', 'paused', 'hold' => DevStatus::ON_HOLD->value,
            default => DevStatus::NOT_STARTED->value,
        };
    }
}
