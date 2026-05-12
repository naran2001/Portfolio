const fs = require('fs');
let content = fs.readFileSync('index.php', 'utf8');

content = content.replace('<!DOCTYPE html>', `<?php
$data = json_decode(file_get_contents('data.json'), true);
?>
<!DOCTYPE html>`);

content = content.replace('src="img/profile.png"', 'src="<?php echo htmlspecialchars($data[\'files\'][\'profile_image\']); ?>"');
content = content.replace('href="cv/Thilina_Narampanawa_CV.pdf"', 'href="<?php echo htmlspecialchars($data[\'files\'][\'cv\']); ?>"');

// Replace the experience timeline with a PHP loop
const experienceHtmlRegex = /<div class="timeline">\s*<div class="timeline-item">[\s\S]*?(?=<\/div>\s*<\/div>\s*<div>\s*<h3 style="font-size: 1\.8rem; margin-bottom: 30px; display: flex; align-items: center; gap: 10px;"><i class="fas fa-graduation-cap")/m;

const phpExperienceLoop = `<div class="timeline">
                    <?php foreach ($data['experience'] as $job): ?>
                    <div class="timeline-item">
                        <div class="timeline-content glass-panel">
                            <div class="timeline-date"><?php echo htmlspecialchars($job['date']); ?></div>
                            <h3 class="timeline-title"><?php echo htmlspecialchars($job['title']); ?></h3>
                            <div class="timeline-subtitle"><?php echo htmlspecialchars($job['company']); ?></div>
                            <ul>
                                <?php foreach ($job['bullets'] as $bullet): ?>
                                <li><?php echo htmlspecialchars($bullet); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>`;

content = content.replace(experienceHtmlRegex, phpExperienceLoop + '\n            ');

fs.writeFileSync('index.php', content);
console.log('index.php updated');
