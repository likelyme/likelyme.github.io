<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI 文生图生成工具</title>
    <style>
        /* 添加表格容器样式 */
        .table-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }

        /* 修改表格样式 */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            background-color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* 添加按钮样式 */
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        /* 添加图片预览样式 */
        #preview {
            width: 100%;
            max-width: 600px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <!-- 添加表格容器 -->
    <div class="table-container">
        <!-- 添加表格用于输入数值 -->
        <table>
            <tbody>
                <tr>
                    <td>提示词</td>
                    <td>
                        <textarea id="prompt" rows="6" cols="50" style="width: 100%; resize: vertical;">an island near sea, with seagulls, moon shining over the sea, light house, boats int he background, fish flying over the sea</textarea>
                    </td>
                </tr>
                <tr>
                    <td>图片尺寸</td>
                    <td>
                        <select id="image_size" style="width: 100%;">
                            <option value="1024x1024">1:1</option>
                            <option value="960x1280">9:16</option>
                            <option value="1280x960">16:9</option>
                            <option value="768x1024">5:3</option>
                            <option value="1024x768">3:5</option>
                            <option value="720x1440">720x1440</option>
                            <option value="720x1280">720x1280</option>
                            <option value="others">others</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>推理步数</td>
                    <td><input type="number" id="num_inference_steps" value="20" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td>指导比例</td>
                    <td><input type="number" id="guidance_scale" value="7.5" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td>负面模型</td>
                    <td>
                        <textarea id="negative_prompt" rows="6" cols="50" style="width: 100%; resize: vertical;"></textarea>
                </tr>
                <tr>
                    <td>相似图片</td>
                    <td>
                        <input type="file" id="image" accept="image/*" style="width: 100%;">
                    </td>
                </tr>
                <tr>
                    <td>图片数量</td>
                    <td>
                        <select id="batch_size" style="width: 100%;">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>

                        </select>
                    </td>
                </tr>
                <!-- 添加图片预览行 -->
                <tr>
                    <td colspan="2">
                        <div id="preview"></div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- 添加按钮用于触发图片生成 -->
        <button onclick="generateImage()">生成图片</button>
    </div>

    <script>
        // 定义生成图片的函数
        function generateImage() {
            // 获取表格中的值
            const model = 'Kwai-Kolors/Kolors'; // 默认模型
            const prompt = document.getElementById('prompt').value;
            const imageSize = document.getElementById('image_size').value;
            const numInferenceSteps = document.getElementById('num_inference_steps').value;
            const guidanceScale = document.getElementById('guidance_scale').value;
            const negativePrompt = document.getElementById('negative_prompt').value;
            const image = 'data:image/png;base64,' + document.getElementById('image').value;
            const batchSize = document.getElementById('batch_size').value;

            // 构造请求体
            const requestBody = {
                model,
                prompt,
                image_size: imageSize,
                batch_size: 1,
                num_inference_steps: parseInt(numInferenceSteps),
                guidance_scale: parseFloat(guidanceScale)
            };

            // 打印请求体
            console.log('Request Body:', requestBody);

            // 发送请求
            fetch('https://api.siliconflow.cn/v1/images/generations', {
                    method: 'POST',
                    headers: {
                        Authorization: 'Bearer sk-aziskgrsleudvdzhgydfdxptvjkjdlrshzusdlpcwktuhewv',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(requestBody)
                })
                .then(response => {
                    // 打印响应对象
                    console.log('Response Object:', response);
                    return response.json();
                })
                .then(response => {
                    // 打印完整响应
                    console.log('Full Response:', response);

                    // 检查返回值中是否存在 images 字段
                    if (response.images && response.images.length > 0) {
                        // 获取第一个图片的 URL
                        const imageUrl = response.images[0].url;
                        // 创建 img 元素并设置 src 属性
                        const imgElement = document.createElement('img');
                        imgElement.src = imageUrl;
                        imgElement.style.width = '100%'; // 设置图片宽度为容器宽度
                        // 将图片添加到预览容器中
                        document.getElementById('preview').innerHTML = ''; // 清空预览容器
                        document.getElementById('preview').appendChild(imgElement);
                    } else {
                        console.error('No images found in the response.');
                        alert('生成图片失败，请检查输入参数！');
                    }
                })
                .catch(err => {
                    // 打印错误信息
                    console.error('Error:', err);
                    alert('请求失败，请稍后重试！');
                });
        }

        // 新增文件上传处理逻辑
        document.getElementById('image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgElement = document.createElement('img');
                    imgElement.src = e.target.result;
                    imgElement.style.width = '100%'; // 设置图片宽度为容器宽度
                    document.getElementById('preview').innerHTML = ''; // 清空预览容器
                    document.getElementById('preview').appendChild(imgElement);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>