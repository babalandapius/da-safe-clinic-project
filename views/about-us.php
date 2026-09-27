<?php
if(!defined('ACCESS_DASHBOARD')) { die('Direct access not allowed.'); }
?>

<div class="workspace-container">
    <div class="workspace-header" style="margin-bottom: 22px;">
        <div>
            <h2 style="margin: 0; color: #1a202c; font-size: 1.5rem;">
                <i class="fas fa-info-circle" style="color: #1b6ca8;"></i> About The System & Project Research
            </h2>
            <p style="color: #718096; margin: 4px 0 0; font-size: 0.9rem;">
                Development of a Web-Based Patient Records System for Da Safe Community Clinic, Nagongera.
            </p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px;">
        <!-- Academic Information Card -->
        <div class="card" style="background: white; padding: 30px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; line-height: 1.6;">
            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 18px;">
                <div style="background: #ebf8ff; color: #1b6ca8; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class="fas fa-university"></i>
                </div>
                <div>
                    <h3 style="margin: 0; color: #1a202c; font-size: 1.2rem;">Busitema University</h3>
                    <p style="margin: 0; color: #718096; font-size: 0.88rem;">Faculty of Science & Education</p>
                </div>
            </div>

            <table style="width: 100%; border-collapse: collapse; font-size: 0.92rem; margin-top: 15px;">
                <tr style="border-bottom: 1px solid #edf2f7;">
                    <td style="padding: 10px 0; font-weight: 600; color: #4a5568; width: 40%;">Project Title:</td>
                    <td style="padding: 10px 0; color: #2d3748;">Development of a Web-Based Patient Records System</td>
                </tr>
                <tr style="border-bottom: 1px solid #edf2f7;">
                    <td style="padding: 10px 0; font-weight: 600; color: #4a5568;">Researcher / Author:</td>
                    <td style="padding: 10px 0; color: #1b6ca8; font-weight: 700;">MWABINI PETER MOSES</td>
                </tr>
                <tr style="border-bottom: 1px solid #edf2f7;">
                    <td style="padding: 10px 0; font-weight: 600; color: #4a5568;">Registration Number:</td>
                    <td style="padding: 10px 0; color: #2d3748;"><code>BU/UP/2023/1755</code></td>
                </tr>
                <tr style="border-bottom: 1px solid #edf2f7;">
                    <td style="padding: 10px 0; font-weight: 600; color: #4a5568;">Department:</td>
                    <td style="padding: 10px 0; color: #2d3748;">Department of Computer Studies</td>
                </tr>
                <tr style="border-bottom: 1px solid #edf2f7;">
                    <td style="padding: 10px 0; font-weight: 600; color: #4a5568;">University Supervisor:</td>
                    <td style="padding: 10px 0; color: #2d3748; font-weight: 600;">Dr. OBOTH ANDREW</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; font-weight: 600; color: #4a5568;">Award:</td>
                    <td style="padding: 10px 0; color: #2d3748;">Bachelor’s Degree in Science Education</td>
                </tr>
            </table>
        </div>

        <!-- Case Study Background -->
        <div class="card" style="background: white; padding: 30px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; line-height: 1.6;">
            <h3 style="margin-top: 0; color: #1a202c; font-size: 1.2rem;">
                <i class="fas fa-clinic-medical" style="color: #00a896;"></i> Case Study: Da Safe Community Clinic
            </h3>
            
            <p style="color: #4a5568; font-size: 0.92rem; margin-top: 10px;">
                <strong>Location:</strong> Nagongera Town Council, Tororo District, Eastern Uganda.
            </p>
            <p style="color: #4a5568; font-size: 0.92rem;">
                <strong>Problem Solved:</strong> Replaces the 100% manual paper-based filing system that caused 15–45 minute search times per file, frequent record misplacement, queue jumping, and tedious 3–4 day monthly HMIS reporting.
            </p>

            <div style="background: #f0fff4; border-left: 4px solid #38a169; padding: 14px 16px; border-radius: 8px; margin-top: 15px; font-size: 0.88rem; color: #22543d;">
                <strong>Key System Accomplishments:</strong>
                <ul style="margin: 6px 0 0 16px;">
                    <li>Unique Patient Codes (e.g. <code>DSC/001</code>) preventing duplicate files.</li>
                    <li>Integrated clinical vital signs & chronological medical consultation history.</li>
                    <li>Instant electronic lab requests & result turnaround with AI insights.</li>
                    <li>Role-Based Access Control ensuring patient data confidentiality.</li>
                </ul>
            </div>
        </div>
    </div>
</div>