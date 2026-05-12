<?php

$html = file_get_contents('index.html');

// Replace About Bio Paragraphs
$html = preg_replace('/(<div class="about-text">\s*<h3>Hello, I\'m <span id="about-name">)[^<]*(<\/span><\/h3>\s*)(<p>)[^<]*(<\/p>\s*<p>)[^<]*(<\/p>\s*<\/div>)/s', 
                     '$1Loading...$2<div id="about-bio-container"><p>Loading...</p></div></div>', $html);

// Replace About Contact Info
$html = str_replace('thilinanarampanawa@gmail.com', '<span id="contact-email">Loading...</span>', $html);
$html = str_replace('+94 71 35 35 964', '<span id="contact-phone">Loading...</span>', $html);
$html = str_replace('No 60, Sirimalwaththa, Gunnapana, Kandy, Sri Lanka', '<span id="contact-location">Loading...</span>', $html);
$html = str_replace('Sinhala, English', '<span id="about-languages">Loading...</span>', $html);

// Education Timeline Container
$html = preg_replace('/(<h3 style="[^"]*">\s*<i class="fas fa-graduation-cap"[^>]*><\/i> Education<\/h3>\s*)<div class="timeline">.*?<\/div>\s*(<\/div>\s*<\/div>\s*<\/section>)/s', 
                     '$1<div id="education-container" class="timeline"></div>'."\n".'            $2', $html);

// Skills Container
$html = preg_replace('/(<h2 class="section-title">Technical Skills<\/h2>\s*)<div class="skills-container">.*?<\/div>\s*(<\/section>)/s', 
                     '$1<div id="skills-container" class="skills-container"></div>'."\n".'    $2', $html);

// Achievements Container
$html = preg_replace('/(<h3 style="[^"]*">\s*<i class="fas fa-trophy"[^>]*><\/i> Achievements<\/h3>\s*<div class="glass-panel"[^\>]*>\s*)<ul style="list-style: none;">.*?<\/ul>\s*(<\/div>\s*<\/div>)/s', 
                     '$1<ul id="achievements-container" style="list-style: none;"></ul>'."\n".'                $2', $html);

// Activities Container
$html = preg_replace('/(<h3 style="[^"]*">\s*<i class="fas fa-users"[^>]*><\/i> Extracurricular Activities<\/h3>\s*<div class="glass-panel"[^\>]*>\s*)<ul style="list-style: none;">.*?<\/ul>\s*(<\/div>\s*<\/div>)/s', 
                     '$1<ul id="activities-container" style="list-style: none;"></ul>'."\n".'                $2', $html);

$load_data_insert = <<<EOD
                // Update About Details
                if(data.about) {
                    if(data.about.bio_paragraphs) {
                        document.getElementById('about-bio-container').innerHTML = data.about.bio_paragraphs.map(p => '<p>' + p + '</p>').join('');
                    }
                    if(document.getElementById('contact-email')) {
                        document.querySelectorAll('#contact-email').forEach(el => el.innerText = data.about.email);
                    }
                    if(document.getElementById('contact-phone')) {
                        document.querySelectorAll('#contact-phone').forEach(el => el.innerText = data.about.phone);
                    }
                    if(document.getElementById('contact-location')) {
                        document.querySelectorAll('#contact-location').forEach(el => el.innerText = data.about.location);
                    }
                    if(document.getElementById('about-languages')) {
                        document.querySelectorAll('#about-languages').forEach(el => el.innerText = data.about.languages);
                    }
                }
                
                // Update Education
                if(data.education) {
                    const eduContainer = document.getElementById('education-container');
                    if (eduContainer) {
                        eduContainer.innerHTML = '';
                        data.education.forEach(edu => {
                            eduContainer.innerHTML += `
                                <div class="timeline-item">
                                    <div class="timeline-content glass-panel">
                                        <div class="timeline-date">\${edu.date}</div>
                                        <h3 class="timeline-title">\${edu.title}</h3>
                                        <div class="timeline-subtitle">\${edu.institution}</div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                }
                
                // Update Skills
                if(data.skills) {
                    const skillsContainer = document.getElementById('skills-container');
                    if(skillsContainer) {
                        skillsContainer.innerHTML = '';
                        data.skills.forEach(skill => {
                            let itemsHtml = '';
                            skill.items.forEach(item => {
                                itemsHtml += `
                                    <div class="skill-item">
                                        <div class="skill-info"><span>\${item.name}</span><span>\${item.percent}%</span></div>
                                        <div class="skill-bar"><div class="skill-progress" data-width="\${item.percent}%"></div></div>
                                    </div>
                                `;
                            });
                            skillsContainer.innerHTML += `
                                <div class="skill-category glass-panel">
                                    <h3><i class="fas \${skill.icon}"></i> \${skill.category}</h3>
                                    \${itemsHtml}
                                </div>
                            `;
                        });
                    }
                }

                // Update Achievements & Activities
                if(data.achievements) {
                    const achContainer = document.getElementById('achievements-container');
                    if(achContainer) {
                        achContainer.innerHTML = '';
                        const icons = ['fa-award', 'fa-camera', 'fa-music', 'fa-trophy', 'fa-star'];
                        data.achievements.forEach((ach, index) => {
                            const icon = icons[index % icons.length];
                            achContainer.innerHTML += `
                                <li style="margin-bottom: 20px; display: flex; gap: 15px; align-items: center;">
                                    <div class="info-icon" style="width: 40px; height: 40px; font-size: 1rem; flex-shrink: 0;"><i class="fas \${icon}"></i></div>
                                    <span style="font-weight: 500; font-size: 1.05rem;">\${ach}</span>
                                </li>
                            `;
                        });
                    }
                }
                
                if(data.activities) {
                    const actContainer = document.getElementById('activities-container');
                    if(actContainer) {
                        actContainer.innerHTML = '';
                        const icons = ['fa-handshake', 'fa-users', 'fa-laptop', 'fa-video', 'fa-comments'];
                        data.activities.forEach((act, index) => {
                            const icon = icons[index % icons.length];
                            actContainer.innerHTML += `
                                <li style="margin-bottom: 20px; display: flex; gap: 15px; align-items: center;">
                                    <div class="info-icon" style="width: 40px; height: 40px; font-size: 1rem; flex-shrink: 0;"><i class="fas \${icon}"></i></div>
                                    <span style="font-weight: 500; font-size: 1.05rem;">\${act}</span>
                                </li>
                            `;
                        });
                    }
                }
EOD;

$html = str_replace('// Update Experience', $load_data_insert . "\n                // Update Experience", $html);

file_put_contents('index.html', $html);
echo "HTML patched successfully.";
