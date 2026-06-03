<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterBarangParentSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $items = [
            'AC',
            'AGGREGAM ANALYZER',
            'ABPM (Ambulatory Blood Pressure Monitor)',
            'ANESTHESIA MACHINE',
            'ANGIOGRAPHY SYSTEM',
            'ANTI VIBRATION TABLE',
            'TONOMETER',
            'LEAD APRON',
            'ARTHROSCOPY SYSTEM',
            'ASPIRATION PUMP',
            'AUDIOMETER',
            'KERATO REFRACTOMETER',
            'VITAL SIGNS MONITOR',
            'AUTOCLAVE',
            'DNA/RNA EXTRACTION SYSTEM',
            'DEFIBRILLATOR',
            'BABY INCUBATOR',
            'BABY SCALE',
            'RESUSCITATOR (BVM)',
            'STRETCHER',
            'PATIENT BED',
            'BEDSIDE CABINET',
            'BEKISTING',
            'BERA SYSTEM',
            'BIOSAFETY CABINET',
            'MICROSCOPE',
            'MEDICAL FREEZER',
            'BIOMETRY SYSTEM',
            'BLANKET WARMER',
            'BLOOD BANK REFRIGERATOR',
            'BLOOD COLLECTION MIXER',
            'BOBATH STOOL',
            'BODY COMPOSITION ANALYZER',
            'ORTHOPEDIC DRILL',
            'CPAP SYSTEM',
            'CAMERA SYSTEM',
            'CPET SYSTEM',
            'C-ARM',
            'ELECTROSURGICAL UNIT (CAUTER)',
            'PATIENT MONITOR',
            'CENTRIFUGE',
            'CHART PROJECTOR',
            'CLINICAL CHEMISTRY ANALYZER',
            'CMS',
            'CO2 INCUBATOR',
            'COLPOSCOPY SYSTEM',
            'COMMODE CHAIR',
            'RADIOGRAPHY SYSTEM (CR/DR)',
            'CPM MACHINE',
            'COOLER BOX',
            'CPR BOARD',
            'CRYO THERAPY SYSTEM',
            'CRYOGENIC TANK',
            'CRYOSTAT',
            'CT SCAN SYSTEM',
            'CT SIMULATOR',
            'CTG',
            'CUSA SYSTEM',
            'UROLOGY ENDOSCOPE',
            'DENTAL UNIT',
            'DIALYSIS CHAIR',
            'DOPPLER',
            'ECG',
            'ECHOCARDIOGRAPHY SYSTEM',
            'EEG',
            'EXAMINATION TABLE',
            'ELECTRIC PATIENT BED',
            'ELECTROTHERAPY UNIT',
            'EMERGENCY TROLLEY',
            'ENT TREATMENT UNIT',
            'ESR ANALYZER',
            'PACEMAKER',
            'SUCTION UNIT',
            'FIBROSCAN',
            'FLAT PANEL DETECTOR',
            'FLOWMETER',
            'GAS CYLINDER SYSTEM',
            'GENOSE PCR',
            'GLUCOMETER',
            'GYNECOLOGY BED',
            'HARMONIC SCALPEL',
            'HEAD IMMOBILIZER',
            'HEAD LAMP',
            'HEART LUNG MACHINE',
            'HEMATOLOGY ANALYZER',
            'HEMOTHERM',
            'HFNC',
            'IABP',
            'IMMUNOLOGY ANALYZER',
            'INFANT INCUBATOR',
            'INFANT WARMER',
            'INFRARED LAMP',
            'INFUSION PUMP',
            'IOL MASTER',
            'IVUS',
            'WHEELCHAIR',
            'LABORATORY INCUBATOR',
            'LAMINAR AIR FLOW',
            'LASER THERAPY',
            'MEDICAL REFRIGERATOR',
            'OXYGEN SYSTEM',
            'PCR SYSTEM',
            'SYRINGE PUMP',
            'UPS',
            'UROFLOWMETER',
            'USG'
        ];

        foreach ($items as $name) {
            DB::table('master_barang_parents')->updateOrInsert(
                ['NamaBarang' => $name],
                [
                    'NamaBarang' => $name,
                    'UserCreated' => 'System',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            );
        }
    }
}
