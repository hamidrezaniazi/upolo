        if (!class_exists('CreateModelHistoriesTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_files_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . 'create_files_table.php'),
            ], 'migrations');
        }
    }
