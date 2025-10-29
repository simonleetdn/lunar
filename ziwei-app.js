// 檔案: ziwei-app.js (Bootstrap 深度整合版)

document.addEventListener('DOMContentLoaded', () => {

    // (地支 Pinyin 對照表 - 不變)
    const branchPinyinMap = {
        '寅': 'yin', '卯': 'mao', '辰': 'chen', '巳': 'si',
        '午': 'wu', '未': 'wei', '申': 'shen', '酉': 'you',
        '戌': 'xu', '亥': 'hai', '子': 'zi', '丑': 'chou'
    };
    
    // (獲取所有 HTML 元素 - 不變)
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

    // (calculateButton 監聽器 - 不變)
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

            const oldPalaces = chartGridDiv.querySelectorAll('.palace');
            oldPalaces.forEach(palace => palace.remove());
            chartInfoDiv.innerHTML = '';
            aiResult.innerHTML = ''; 

            renderChartInfo(astrolabe, name); 
            renderChartGrid(astrolabe); 

            chartContainer.style.display = 'block'; 
            downloadImageButton.style.display = 'inline-block'; 
            aiAnalysisContainer.style.display = 'block'; 

        } catch (error)
        {
            console.error('排盤失敗:', error);
            currentAstrolabe = null; 
            alert('排盤失敗，請檢查輸入的日期或時辰。');
        }
    });

    /**
     * *** 修改：renderChartInfo ***
     * 將中宮資訊格式化為 Bootstrap Card Body
     */
    function renderChartInfo(astrolabe, name) {
        // 使用 .card-body 和 .card-title
        const infoHtml = `
        <div class="card-body text-center">
            <h4 class="card-title">${name} 的命盤</h4>
            <div class="card-text">
                <p class="mb-1 small text-muted"><strong>陽曆：</strong> ${astrolabe.solarDate}</p>
                <p class="mb-1 small text-muted"><strong>農曆：</strong> ${astrolabe.lunarDate}</p>
                <p class="mb-1 small"><strong>命主：</strong> ${astrolabe.soul}</p>
                <p class="mb-1 small"><strong>身主：</strong> ${astrolabe.body}</p>
                <p class="mb-1 small"><strong>五行局：</strong> ${astrolabe.fiveElementsClass}</p>
            </div>
        </div>
        `;
        chartInfoDiv.innerHTML = infoHtml;
    }

    /**
     * *** 修改：renderChartGrid ***
     * (這個函式在上一步已經 Bootstrap 化了，無需變更)
     */
    function renderChartGrid(astrolabe) {
        astrolabe.palaces.forEach(palace => {
            const palaceDiv = document.createElement('div');
            palaceDiv.className = 'palace card h-100'; 
            
            const pinyin = branchPinyinMap[palace.earthlyBranch];
            if (pinyin) {
                palaceDiv.classList.add('palace-' + pinyin);
            }

            let innerHtml = `
                <div class="card-header p-2 text-center font-weight-bold" style="font-size: 1.1rem;">
                    ${palace.name}
                </div>
                <div class="card-body p-2" style="min-height: 120px;">
                    ${palace.majorStars.map(star => 
                        `<span class="badge badge-danger mr-1 mb-1">${star.name}</span>`
                    ).join('')}
                    ${palace.minorStars.map(star => 
                        `<span class="badge badge-primary mr-1 mb-1">${star.name}</span>`
                    ).join('')}
                    ${palace.adjectiveStars.map(star => 
                        `<span class="badge badge-success mr-1 mb-1">${star.name}</span>`
                    ).join('')}
                </div>
                <div class="card-footer p-2 text-muted small">
                    <div class="clearfix">
                        <span class="float-left font-weight-bold" style="font-size: 1.1em;">${palace.earthlyBranch}</span>
                        <span class="float-right">${palace.heavenlyStem}</span>
                    </div>
                    <div class="mt-1">
                        <span class="badge badge-pill badge-light mr-1">長生: ${palace.changsheng12}</span>
                        <span class="badge badge-pill badge-light">博士: ${palace.boshi12}</span>
                    </div>
                    <div class="text-dark font-weight-bold text-center mt-1" style="font-size: 0.9em;">
                        大限 (${palace.decadal.range.join('-')})
                    </div>
                </div>
            `;
            palaceDiv.innerHTML = innerHtml;
            chartGridDiv.appendChild(palaceDiv);
        });
    }


    // (downloadImageButton 監聽器 - 不變)
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

    // (formatStarsForAI 函式 - 不變)
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

    /**
     * *** 修改：generateAiButton 監聽器 ***
     * 將 AI 結果的 HTML 也改為 Bootstrap 樣式
     */
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

            // (prompt 產生邏輯 - 不變)
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
(分析要求內容不變...)
1.  **[總論]**：...
2.  **[性格與才能]**：...
3.  **[事業與工作]**：...
4.  **[財富與理財]**：...
5.  **[感情與人際]**：...
6.  **[健康與關注重點]**：...
7.  **[總結建議]**：...
`;
            
            // *** 修改：AI 結果的 HTML ***
            aiResult.innerHTML = `
                <button id="copyPromptButton" class="btn btn-secondary btn-sm" style="position: absolute; top: 1rem; right: 1rem;">複製提示詞</button>
                
                <h5 class="card-title mt-2"><span role="img" aria-label="copy">📋</span> 您的專屬 AI 提示詞已產生</h5>
                
                <p class="card-text small text-muted">
                    我們已經根據您的命盤，產生了一段非常詳細的提示詞。
                    <br>
                    <strong>請點擊右上方按鈕複製</strong>，然後貼到您慣用的 AI 服務（例如 Google Gemini, ChatGPT, Claude 等）中，即可獲得深入的命盤解讀。
                </p>
                
                <pre class="pre-scrollable bg-light p-3" style="max-height: 400px;"><code>${prompt.trim()}</code></pre>
            `;

        }, 1500); // 模擬 1.5 秒的生成時間
    });


    // (aiAnalysisContainer 監聽器，用於 "一鍵複製" - 不變)
    aiAnalysisContainer.addEventListener('click', (event) => {
        // 監聽 ID，所以按鈕樣式改變不影響
        if (event.target.id === 'copyPromptButton') {
            const preBlock = aiResult.querySelector('pre');
            if (preBlock) {
                const promptText = preBlock.textContent;
                navigator.clipboard.writeText(promptText)
                    .then(() => {
                        const button = event.target;
                        button.textContent = '已複製！';
                        // 改為 Bootstrap 成功樣式
                        button.classList.remove('btn-secondary');
                        button.classList.add('btn-success');
                        
                        setTimeout(() => {
                            button.textContent = '複製提示詞';
                            button.classList.remove('btn-success');
                            button.classList.add('btn-secondary');
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