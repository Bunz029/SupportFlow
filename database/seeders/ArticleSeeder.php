<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin and agent users
        $admins = User::role('admin')->get();
        $agents = User::role('agent')->get();
        $authors = $admins->merge($agents);
        
        // Get categories
        $categories = Category::all();

        // Create knowledge base articles
        $articles = [
            [
                'title' => 'How to Reset Your Password',
                'content' => "
                    # How to Reset Your Password

                    If you've forgotten your password, don't worry. Follow these simple steps to reset it:

                    1. Click on the 'Forgot Password' link on the login page.
                    2. Enter your email address associated with your account.
                    3. Check your email for a password reset link.
                    4. Click on the link and follow the instructions to create a new password.
                    5. Use your new password to log in.

                    If you don't receive the email within a few minutes, check your spam folder.
                ",
                'tags' => ['password', 'account', 'login'],
                'visibility' => 'public',
            ],
            [
                'title' => 'Understanding Ticket Priority Levels',
                'content' => "
                    # Understanding Ticket Priority Levels

                    Our support system uses the following priority levels to classify support tickets:

                    ## Low Priority
                    - General questions
                    - Feature requests
                    - Cosmetic issues

                    ## Medium Priority
                    - Minor functionality issues
                    - Configuration problems
                    - Account changes

                    ## High Priority
                    - Service degradation
                    - Major functionality issues
                    - Issues affecting multiple users

                    ## Urgent Priority
                    - Complete service outage
                    - Data loss or security breach
                    - Critical business impact

                    Each priority level has an associated Service Level Agreement (SLA) for response and resolution times.
                ",
                'tags' => ['tickets', 'priority', 'support'],
                'visibility' => 'public',
            ],
            [
                'title' => 'Submitting an Effective Support Ticket',
                'content' => "
                    # Submitting an Effective Support Ticket

                    To get the fastest and most effective help, please include the following information when submitting a support ticket:

                    1. **Descriptive Subject Line** - Briefly summarize the issue.
                    2. **Detailed Description** - Explain the problem in detail, including:
                       - What you were trying to do
                       - What happened instead
                       - Any error messages you received
                    3. **Steps to Reproduce** - List the exact steps to recreate the issue.
                    4. **Screenshots or Videos** - Visual evidence helps us understand the problem faster.
                    5. **Environment Information** - Browser type/version, operating system, device type.

                    The more information you provide, the quicker we can resolve your issue.
                ",
                'tags' => ['tickets', 'support', 'best practices'],
                'visibility' => 'public',
            ],
            [
                'title' => 'Using the Knowledge Base',
                'content' => "
                    # Using the Knowledge Base

                    Our Knowledge Base contains articles to help you solve common problems and learn more about our services.

                    ## Finding Articles
                    - Use the search bar at the top of the page
                    - Browse by category using the sidebar
                    - Filter articles by tags

                    ## Article Types
                    - **How-to Guides**: Step-by-step instructions
                    - **Troubleshooting**: Solutions to common problems
                    - **FAQs**: Answers to frequently asked questions
                    - **Release Notes**: Information about new features and updates

                    ## Didn't Find What You Need?
                    If you can't find an answer to your question, please submit a support ticket and our team will assist you.
                ",
                'tags' => ['knowledge base', 'help', 'documentation'],
                'visibility' => 'public',
            ],
            [
                'title' => 'Internal: Escalation Procedures',
                'content' => "
                    # Escalation Procedures for Support Agents

                    This document outlines the proper escalation procedures for support tickets that cannot be resolved at the first level.

                    ## Level 1 to Level 2 Escalation
                    If you cannot resolve a ticket within the first hour of assignment, or if it requires specialized knowledge, escalate to Level 2 support by:
                    1. Adding the 'L2-Support' tag to the ticket
                    2. Adding a private comment explaining the reason for escalation
                    3. Changing the assignee to the appropriate L2 team

                    ## Level 2 to Level 3 Escalation
                    For tickets requiring developer intervention or executive attention:
                    1. Add the 'L3-Support' tag to the ticket
                    2. Create a detailed summary of troubleshooting steps taken
                    3. Notify the development team lead via the #dev-escalations Slack channel
                    4. Update the customer that their issue is being investigated by our technical team

                    ## Critical Incident Protocol
                    For service outages or security incidents:
                    1. Immediately notify the on-call engineer
                    2. Add the 'Critical-Incident' tag
                    3. Follow the incident response procedure in the company handbook
                ",
                'tags' => ['procedures', 'escalation', 'internal'],
                'visibility' => 'internal',
            ],
        ];

        foreach ($articles as $articleData) {
            $article = new Article([
                'title' => $articleData['title'],
                'slug' => Str::slug($articleData['title']),
                'content' => $articleData['content'],
                'tags' => $articleData['tags'],
                'visibility' => $articleData['visibility'],
                'category_id' => $categories->random()->id,
                'author_id' => $authors->random()->id,
            ]);
            
            $article->save();
        }
    }
} 