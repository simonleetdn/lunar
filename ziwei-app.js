// 檔案: ziwei-app.js (內容與 app.js 相同)

document.addEventListener('DOMContentLoaded', () => {

    // (地支 Pinyin 對照表... 等頂層變數不變)
    const branchPinyinMap = {
        '寅': 'yin', '卯': 'mao', '辰': 'chen', '巳': 'si',
        '午': 'wu', '未': 'wei', '申': 'shen', '酉': 'you',
        '戌': 'xu', '亥': 'hai', '子': 'zi', '丑': 'chou'
    };
    
    const nameInput = document.getElementById('name');
    const birthDateInput = document.getElementById('birthDate');
    const birthTimeInput = document.getElementById('birthTime');
    const genderInput = document.getElementById('gender');
    const calculateButton = document.getElementById('calculateButton');
    const chartInfoDiv = document.getElementById('chart-info');
    const chartGridDiv = document.getElementById('chart-grid');
    const downloadImageButton = document.getElementById('downloadImageButton');
    const chartContainer = document.getElementById('chart-container'); 
    const aiAnalysisContainer = document.getElementById('ai-analysis-container');
    const generateAiButton = document.getElementById('generateAiButton');
    const aiLoading = document.getElementById('ai-loading');
    const aiResult = document.getElementById('ai-result');

    let currentAstrolabe = null; 

// --- 主按鈕：開始排盤 ---
    calculateButton.addEventListener('click', () => {
        const name = nameInput.value || '（未輸入姓名）';
        const solarDate = birthDateInput.value;
        const timeIndex = parseInt(birthTimeInput.value, 10); 
        const genderValue = genderInput.value;
        
        if (!solarDate) {
            alert('請選擇出生日期');
            return;
        }
        
        const genderChinese = (genderValue === 'male') ? '男' : '女';

        try {
            const astrolabe = iztro.astro.bySolar(solarDate, timeIndex, genderChinese, true, 'zh-TW');
            currentAstrolabe = astrolabe; 

            // (清空舊宮位的程式碼...)
            const oldPalaces = chartGridDiv.querySelectorAll('.palace');
            oldPalaces.forEach(palace => palace.remove());
            chartInfoDiv.innerHTML = '';
            aiResult.innerHTML = ''; 

            // (渲染邏輯...)
            renderChartInfo(astrolabe, name); 
            renderChartGrid(astrolabe); 

            // *** 核心修改：在渲染完成後，顯示所有隱藏的區塊 ***
            chartContainer.style.display = 'block'; // 顯示命盤
            downloadImageButton.style.display = 'inline-block'; // 顯示 PNG 按鈕
            aiAnalysisContainer.style.display = 'block'; // 顯示 AI 區塊

        } catch (error)
        {
            console.error('排盤失敗:', error);
            currentAstrolabe = null; 
            alert('排盤失敗，請檢查輸入的日期或時辰。');
        }
    });

    // (renderChartInfo 函式不變)
    function renderChartInfo(astrolabe, name) {
        const infoHtml = `
            <h3>${name} 的命盤</h3>
            <p><strong>陽曆：</strong> ${astrolabe.solarDate}</p>
            <p><strong>農曆：</strong> ${astrolabe.lunarDate}</p>
            <p><strong>命主：</strong> ${astrolabe.soul}</p>
            <p><strong>身主：</strong> ${astrolabe.body}</p>
            <p><strong>五行局：</strong> ${astrolabe.fiveElementsClass}</p>
        `;
        chartInfoDiv.innerHTML = infoHtml;
    }

    // (renderChartGrid 函式不變)
    function renderChartGrid(astrolabe) {
        astrolabe.palaces.forEach(palace => {
            const palaceDiv = document.createElement('div');
            palaceDiv.className = 'palace'; 
            const pinyin = branchPinyinMap[palace.earthlyBranch];
            if (pinyin) {
                palaceDiv.classList.add('palace-' + pinyin);
            }
            let innerHtml = `
                <div class="palace-name">${palace.name}</div>
                <div class="palace-heavenly-stem">${palace.heavenlyStem}</div>
                <div class="palace-earthly-branch">${palace.earthlyBranch}</div>
                <div class="stars">
                    ${palace.majorStars.map(star => `<span class="star major">${star.name}</span>`).join('')}
                    ${palace.minorStars.map(star => `<span class="star minor">${star.name}</span>`).join('')}
                    ${palace.adjectiveStars.map(star => `<span class="star adjective">${star.name}</span>`).join('')}
                </div>
                <div class="other-stars">
                    <span class="star dr-star">長生: ${palace.changsheng12}</span>
                    <span class="star dr-star">博士: ${palace.boshi12}</span>
                </div>
                <div class="palace-decade">
                    大限 (${palace.decadal.range.join('-')})
                </div>
            `;
            palaceDiv.innerHTML = innerHtml;
            chartGridDiv.appendChild(palaceDiv);
        });
    }

    // (downloadImageButton 監聽器不變)
    downloadImageButton.addEventListener('click', () => {
        if (!currentAstrolabe) {
            alert('請先排盤！');
            return;
        }
        const name = nameInput.value || '命盤';
        const filename = `${name}_${currentAstrolabe.solarDate}_紫微斗數命盤.png`;
        const elementToPrint = document.getElementById('chart-container');
        html2canvas(elementToPrint, { 
            scale: 2,
            useCORS: true
        }).then(canvas => {
            const imageDataUrl = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.href = imageDataUrl;
            link.download = filename; 
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    });

    // (formatStarsForAI 函式不變)
    function formatStarsForAI(stars) {
        if (!stars || stars.length === 0) {
            return '無';
        }
        return stars.map(star => {
            let starInfo = star.name;
            if (star.brightness) {
                starInfo += `(${star.brightness})`;
            }
            if (star.mutagen) {
                starInfo += `[${star.mutagen}]`;
            }
            return starInfo;
        }).join('、');
    }

    // (generateAiButton 監聽器不變)
    generateAiButton.addEventListener('click', () => {
        if (!currentAstrolabe) {
            alert('請先排盤！');
            return;
        }

        aiLoading.style.display = 'block';
        aiResult.innerHTML = '';
        generateAiButton.style.display = 'none';

        setTimeout(() => {
            aiLoading.style.display = 'none';
            generateAiButton.style.display = 'block';

            let prompt = `
# 角色
你是一位精通現代紫微斗數的命理大師，擅長用友善、鼓勵且易於理解的語言來分析命盤。

# 任務
請根據我提供的紫微斗數命盤資料，為我提供一份詳細的個人命盤解讀。

# 命盤基本資料
* 姓名：${nameInput.value || '（未提供）'}
* 性別：${currentAstrolabe.gender}
* 陽曆生日：${currentAstrolabe.solarDate}
* 農曆生日：${currentAstrolabe.lunarDate}
* 時辰：${currentAstrolabe.time}
* 命主：${currentAstrolabe.soul}
* 身主：${currentAstrolabe.body}
* 五行局：${currentAstrolabe.fiveElementsClass}

# 十二宮位星曜詳情
`;
            currentAstrolabe.palaces.forEach(palace => {
                let palaceType = "";
                if (palace.name === "命宮" && palace.isBodyPalace) {
                    palaceType = " (命宮 / 身宮)";
                } else if (palace.name === "命宮") {
                    palaceType = " (命宮)";
                } else if (palace.isBodyPalace) {
                    palaceType = " (身宮)";
                }

                prompt += `
## ${palace.name}${palaceType} (天干:${palace.heavenlyStem} 地支:${palace.earthlyBranch})
* **主星**：${formatStarsForAI(palace.majorStars)}
* **副星**：${formatStarsForAI(palace.minorStars)}
* **雜曜**：${formatStarsForAI(palace.adjectiveStars)}
`;
            });

            prompt += `
# 分析要求
請根據以上完整的命盤資料，為我分析以下幾個面向：

1.  **[總論]**：我的整體格局是什麼？（例如：殺破狼、機月同梁等），我的命宮和身宮組合，揭示了我的先天性格與後天發展的核心特質是什麼？
2.  **[性格與才能]**：深入分析我的命宮與福德宮，我的優點和缺點分別是什麼？我有哪些潛在的天賦？
3.  **[事業與工作]**：分析我的官祿宮與財帛宮，我適合從事哪方面的工作？（例如：創業、穩定上班、技術型、管理型等）。我未來事業發展的潛力如何？
4.  **[財富與理財]**：分析我的財帛宮與田宅宮，我的財運如何？我適合哪種理財或投資方式？
5.  **[感情與人際]**：分析我的夫妻宮與僕役宮（朋友宮），我的感情觀是什麼？我適合什麼樣的伴侶？我的人際關係狀況如何？
6.  **[健康與關注重點]**：分析我的疾厄宮與父母宮（相貌宮），我需要注意哪些先天的健康問題？
7.  **[總結建議]**：綜合以上所有分析，請給我 3 個最關鍵的人生建議，幫助我揚長避短。

請用繁體中文回答，並盡量保持樂觀和建設性的語氣。
`;
            
            aiResult.innerHTML = `
                <button id="copyPromptButton">複製提示詞</button>
                <h3><span role="img" aria-label="copy">📋</span> 您的專屬 AI 提示詞已產生</h3>
                <p>
                    我們已經根據您的命盤，產生了一段非常詳細的提示詞。
                    <br>
                    <strong>請點擊右上方按鈕複製</strong>，然後貼到您慣用的 AI 服務（例如 Google Gemini, ChatGPT, Claude 等）中，即可獲得深入的命盤解讀。
                </p>
                <pre><code>${prompt.trim()}</code></pre>
            `;

        }, 1500); 
    });


    // (aiAnalysisContainer 監聽器，用於 "一鍵複製" - 不變)
    aiAnalysisContainer.addEventListener('click', (event) => {
        if (event.target.id === 'copyPromptButton') {
            const preBlock = aiResult.querySelector('pre');
            if (preBlock) {
                const promptText = preBlock.textContent;
                navigator.clipboard.writeText(promptText)
                    .then(() => {
                        const button = event.target;
                        button.textContent = '已複製！';
                        button.style.backgroundColor = '#27ae60'; 
                        setTimeout(() => {
                            button.textContent = '複製提示詞';
                            button.style.backgroundColor = '#ff9800';
                        }, 2000);
                    })
                    .catch(err => {
                        console.error('複製失敗: ', err);
                        alert('複製失敗，瀏覽器可能不支援或未授予權限。');
                    });
            }
        }
    });
});