import re

with open('index.html', 'r', encoding='utf-8') as f:
    html = f.read()

# Replace About Bio Paragraphs
html = re.sub(r'(<div class="about-text">\s*<h3>Hello, I\'m <span id="about-name">)[^<]*(</span></h3>\s*)(<p>)[^<]*(</p>\s*<p>)[^<]*(</p>\s*</div>)',
              r'\1Loading...\2<div id="about-bio-container"><p>Loading...</p></div></div>', html, flags=re.DOTALL)

# Replace About Contact Info
html = html.replace('thilinanarampanawa@gmail.com', '<span id="contact-email">Loading...</span>')
html = html.replace('+94 71 35 35 964', '<span id="contact-phone">Loading...</span>')
html = html.replace('No 60, Sirimalwaththa, Gunnapana, Kandy, Sri Lanka', '<span id="contact-location">Loading...</span>')
html = html.replace('Sinhala, English', '<span id="about-languages">Loading...</span>')

# Education Timeline Container
html = re.sub(r'(<h3 style="[^"]*">\s*<i class="fas fa-graduation-cap"[^>]*></i> Education</h3>\s*)<div class="timeline">.*?</div>\s*(</div>\s*</div>\s*</section>)',
              r'\1<div id="education-container" class="timeline"></div>\n            \2', html, flags=re.DOTALL)

# Skills Container
html = re.sub(r'(<h2 class="section-title">Technical Skills</h2>\s*)<div class="skills-container">.*?</div>\s*(</section>)',
              r'\1<div id="skills-container" class="skills-container"></div>\n    \2', html, flags=re.DOTALL)

# Achievements Container
html = re.sub(r'(<h3 style="[^"]*">\s*<i class="fas fa-trophy"[^>]*></i> Achievements</h3>\s*<div class="glass-panel"[^\>]*>\s*)<ul style="list-style: none;">.*?</ul>\s*(</div>\s*</div>)',
              r'\1<ul id="achievements-container" style="list-style: none;"></ul>\n                \2', html, flags=re.DOTALL)

# Activities Container
html = re.sub(r'(<h3 style="[^"]*">\s*<i class="fas fa-users"[^>]*></i> Extracurricular Activities</h3>\s*<div class="glass-panel"[^\>]*>\s*)<ul style="list-style: none;">.*?</ul>\s*(</div>\s*</div>)',
              r'\1<ul id="activities-container" style="list-style: none;"></ul>\n                \2', html, flags=re.DOTALL)

# Add loadData updates
load_data_insert = """
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
                                        <div class="timeline-date">${edu.date}</div>
                                        <h3 class="timeline-title">${edu.title}</h3>
                                        <div class="timeline-subtitle">${edu.institution}</div>
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
                                        <div class="skill-info"><span>${item.name}</span><span>${item.percent}%</span></div>
                                        <div class="skill-bar"><div class="skill-progress" data-width="${item.percent}%"></div></div>
                                    </div>
                                `;
                            });
                            skillsContainer.innerHTML += `
                                <div class="skill-category glass-panel">
                                    <h3><i class="fas ${skill.icon}"></i> ${skill.category}</h3>
                                    ${itemsHtml}
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
                                    <div class="info-icon" style="width: 40px; height: 40px; font-size: 1rem; flex-shrink: 0;"><i class="fas ${icon}"></i></div>
                                    <span style="font-weight: 500; font-size: 1.05rem;">${ach}</span>
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
                                    <div class="info-icon" style="width: 40px; height: 40px; font-size: 1rem; flex-shrink: 0;"><i class="fas ${icon}"></i></div>
                                    <span style="font-weight: 500; font-size: 1.05rem;">${act}</span>
                                </li>
                            `;
                        });
                    }
                }
"""
html = html.replace('// Update Experience', load_data_insert + '\n                // Update Experience')

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(html)
