<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EnrichmentMock;

class EnrichmentMockController extends Controller
{
    /**
     * Mock endpoint to simulate enrichment data for different event types.
     */
    public function enrich(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('event_id');
        $enrichmentData = $request->input('enrichment_data');
        $enrichmentMock = new EnrichmentMock();

        switch ($type) {
            case 'form_provider':
                $enrichmentData = $enrichmentMock->get_form_provider_enrichment();
                break;
            case 'payment_gateway':
                $enrichmentData = $enrichmentMock->get_payment_gateway_enrichment();
                break;
            case 'status_tracker':
                $enrichmentData = $enrichmentMock->get_status_tracker_enrichment();
                break;
            default:
                return response()->json(['error' => 'Unknown source'], 400);
        }

        return response()->json([
            'event_id' => $id,
            'enrichment_data' => $enrichmentData
        ]);
    }

}
