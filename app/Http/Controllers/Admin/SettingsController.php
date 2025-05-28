<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * The cache key used for storing system settings
     */
    const SETTINGS_CACHE_KEY = 'system_settings';

    /**
     * Display the settings page
     */
    public function index()
    {
        $settings = $this->getSettings();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update SLA settings
     */
    public function updateSla(Request $request)
    {
        try {
            $validated = $request->validate([
                'high_priority_sla' => 'required|integer|min:1',
                'medium_priority_sla' => 'required|integer|min:1',
                'low_priority_sla' => 'required|integer|min:1',
                'resolution_time_sla' => 'required|integer|min:1',
            ]);

            $settings = $this->getSettings();
            $settings = array_merge($settings, $validated);

            $this->saveSettings($settings);

            return redirect()->route('admin.settings.index')
                ->with('success', 'SLA settings updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating SLA settings', [
                'error' => $e->getMessage(),
                'inputs' => $request->all()
            ]);
            
            return redirect()->route('admin.settings.index')
                ->with('error', 'Failed to update SLA settings. Please try again.');
        }
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request)
    {
        try {
            $settings = $this->getSettings();
            
            // Handle checkboxes - they're only present in the request if checked
            $settings['notify_on_ticket_creation'] = $request->has('notify_on_ticket_creation');
            $settings['notify_on_sla_breach'] = $request->has('notify_on_sla_breach');
            $settings['notify_on_feedback'] = $request->has('notify_on_feedback');

            $this->saveSettings($settings);

            return redirect()->route('admin.settings.index')
                ->with('success', 'Notification settings updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating notification settings', [
                'error' => $e->getMessage(),
                'inputs' => $request->all()
            ]);
            
            return redirect()->route('admin.settings.index')
                ->with('error', 'Failed to update notification settings. Please try again.');
        }
    }

    /**
     * Get SLA settings as JSON
     */
    public function getSlaSettings()
    {
        $settings = $this->getSettings();
        return response()->json([
            'high_priority_sla' => $settings['high_priority_sla'] ?? 2,
            'medium_priority_sla' => $settings['medium_priority_sla'] ?? 8,
            'low_priority_sla' => $settings['low_priority_sla'] ?? 24,
            'resolution_time_sla' => $settings['resolution_time_sla'] ?? 72,
        ]);
    }

    /**
     * Get notification settings as JSON
     */
    public function getNotificationSettings()
    {
        $settings = $this->getSettings();
        return response()->json([
            'notify_on_ticket_creation' => $settings['notify_on_ticket_creation'] ?? true,
            'notify_on_sla_breach' => $settings['notify_on_sla_breach'] ?? true,
            'notify_on_feedback' => $settings['notify_on_feedback'] ?? true,
        ]);
    }

    /**
     * Helper method to get all settings
     */
    private function getSettings()
    {
        $settings = Cache::get(self::SETTINGS_CACHE_KEY);
        
        // If settings are null, initialize with defaults
        if ($settings === null) {
            $settings = [
                'high_priority_sla' => 2,
                'medium_priority_sla' => 8,
                'low_priority_sla' => 24,
                'resolution_time_sla' => 72,
                'notify_on_ticket_creation' => true,
                'notify_on_sla_breach' => true,
                'notify_on_feedback' => true,
                'moderate_feedback' => false,
                'inappropriate_words' => '',
            ];
            $this->saveSettings($settings);
        }
        
        return $settings;
    }
    
    /**
     * Helper method to save settings
     */
    private function saveSettings($settings)
    {
        // Ensure we're working with an array
        if (!is_array($settings)) {
            $settings = (array) $settings;
        }
        
        // Save to cache
        Cache::forever(self::SETTINGS_CACHE_KEY, $settings);
        
        // For debugging
        Log::info('System settings updated', [
            'settings' => $settings
        ]);
        
        return true;
    }
} 