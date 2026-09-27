<?php
if(!defined('ACCESS_DASHBOARD')) { die('Direct access not allowed.'); }
?>

<div class="workspace-container">
    <div class="workspace-header" style="margin-bottom: 22px;">
        <div>
            <h2 style="margin: 0; color: #1a202c; font-size: 1.5rem;">
                <i class="fas fa-stethoscope" style="color: #1b6ca8;"></i> Clinical & Diagnostic Services
            </h2>
            <p style="color: #718096; margin: 4px 0 0; font-size: 0.9rem;">
                Core healthcare and medical laboratory services offered at Da Safe Community Clinic, Nagongera.
            </p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        <div class="card" style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; border-top: 4px solid #1b6ca8;">
            <h3 style="margin-top: 0; color: #2d3748; font-size: 1.15rem;"><i class="fas fa-user-md" style="color: #1b6ca8;"></i> Outpatient Consultation (OPD)</h3>
            <p style="color: #718096; font-size: 0.9rem; line-height: 1.5; margin: 8px 0 0;">
                Comprehensive general medical consultations, triage vital signs recording, disease diagnosis, and customized patient prescription management.
            </p>
        </div>

        <div class="card" style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; border-top: 4px solid #00a896;">
            <h3 style="margin-top: 0; color: #2d3748; font-size: 1.15rem;"><i class="fas fa-microscope" style="color: #00a896;"></i> Medical Laboratory Analysis</h3>
            <p style="color: #718096; font-size: 0.9rem; line-height: 1.5; margin: 8px 0 0;">
                Malaria microscopy & RDT, Complete Blood Count (CBC), Typhoid Widal agglutination, Urinalysis, Blood Glucose monitoring, and HIV Rapid Diagnostic Testing.
            </p>
        </div>

        <div class="card" style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; border-top: 4px solid #dd6b20;">
            <h3 style="margin-top: 0; color: #2d3748; font-size: 1.15rem;"><i class="fas fa-heartbeat" style="color: #dd6b20;"></i> Emergency & STAT Triage</h3>
            <p style="color: #718096; font-size: 0.9rem; line-height: 1.5; margin: 8px 0 0;">
                Rapid priority laboratory processing and emergency stabilization for critical patients arriving at Nagongera Town Council clinic.
            </p>
        </div>
    </div>
</div>
