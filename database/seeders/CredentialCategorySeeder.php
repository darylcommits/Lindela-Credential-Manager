<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CredentialCategory;

class CredentialCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Email Accounts',
                'slug' => 'email-accounts',
                'description' => 'Gmail, Outlook, and other email service accounts',
                'icon' => 'heroicon-o-envelope',
                'color' => '#EF4444',
            ],
            [
                'name' => 'Social Media',
                'slug' => 'social-media',
                'description' => 'Facebook, Instagram, LinkedIn, Twitter, and other social platforms',
                'icon' => 'heroicon-o-share',
                'color' => '#8B5CF6',
            ],
            [
                'name' => 'Internal Systems',
                'slug' => 'internal-systems',
                'description' => 'Company internal systems and applications',
                'icon' => 'heroicon-o-server',
                'color' => '#10B981',
            ],
            [
                'name' => 'Website Accounts',
                'slug' => 'website-accounts',
                'description' => 'Website registrations and subscriptions',
                'icon' => 'heroicon-o-globe-alt',
                'color' => '#F59E0B',
            ],
            [
                'name' => 'Backup Codes',
                'slug' => 'backup-codes',
                'description' => 'Gmail backup codes and other service backup codes',
                'icon' => 'heroicon-o-shield-check',
                'color' => '#3B82F6',
            ],
            [
                'name' => 'Recovery Codes',
                'slug' => 'recovery-codes',
                'description' => 'Meta recovery codes and other platform recovery codes',
                'icon' => 'heroicon-o-key',
                'color' => '#EC4899',
            ],
            [
                'name' => 'Development Tools',
                'slug' => 'development-tools',
                'description' => 'GitHub, GitLab, AWS, and other development platform accounts',
                'icon' => 'heroicon-o-code-bracket',
                'color' => '#6366F1',
            ],
            [
                'name' => 'Financial Services',
                'slug' => 'financial-services',
                'description' => 'Banking, payment processors, and financial platform accounts',
                'icon' => 'heroicon-o-banknotes',
                'color' => '#059669',
            ],
        ];

        foreach ($categories as $category) {
            CredentialCategory::create($category);
        }
    }
}
