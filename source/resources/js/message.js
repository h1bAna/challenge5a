

function processData(data, userID) {
    // reverse data
    data = data.reverse();

    // Lấy phần tử conversationDetail
    let conversationDetail = document.getElementById('conversationDetail');

    // Xóa nội dung cũ trong conversationDetail
    conversationDetail.innerHTML = '';

    // Tạo các tin nhắn từ dữ liệu và thêm vào conversationDetail
    // `<div class="card-body"><h5>${conversation.title}</h5><ul>${conversation.messages.map(message => `<li>${message}</li>`).join("")}</ul></div>`
    data.forEach(message => {
        // create form
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = 'message.php';
        form.classList.add('card-body');

        if (message.sender_id == userID) {
            form.classList.add('text-right');
        }

        // create textarea
        let textarea = document.createElement('textarea');
        textarea.name = 'message';
        textarea.textContent = message.message;

        // create edit button
        let editButton = document.createElement('button');
        // type submit to submit form
        editButton.type = 'submit';
        editButton.id = 'edit-btn';
        editButton.name = 'edit-btn';
        editButton.textContent = 'Edit';
        editButton.classList.add('edit-btn');

        // create delete button
        let deleteButton = document.createElement('button');
        // type submit to submit form
        deleteButton.type = 'submit';
        deleteButton.id = 'delete-btn';
        deleteButton.name = 'delete-btn';
        deleteButton.textContent = 'Delete';
        deleteButton.classList.add('delete-btn');

        // create hidden id of message
        let idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = message.id;
        idInput.readOnly = true;

        content = `<div>`;
        content += textarea.outerHTML;
        content += `<br>`;
        content += editButton.outerHTML;
        content += deleteButton.outerHTML;
        content += idInput.outerHTML;
        content += `</div>`;
        form.innerHTML = content;
        conversationDetail.appendChild(form);
    });

    
    // reload html

}


function loadConversation(userId1, userId2) {

    // Sử dụng document.querySelector để chọn input theo id hoặc name
    const userTokenInput = document.querySelector('input[name="user_token"]');
    // Hoặc sử dụng document.getElementById('userTokenInput') nếu input có id là 'userTokenInput'

    // Lấy giá trị của input hidden
    const userTokenValue = userTokenInput.value;

    const currentTime = new Date();

    // Tạo hàm helper để thêm số 0 phía trước nếu cần
    const addZero = (number) => (number < 10 ? '0' + number : number);

    // Trích xuất các thành phần của thời gian
    const year = currentTime.getFullYear();
    const month = addZero(currentTime.getMonth() + 1); // Tháng trong JavaScript bắt đầu từ 0, nên cần cộng thêm 1
    const day = addZero(currentTime.getDate());
    const hours = addZero(currentTime.getHours());
    const minutes = addZero(currentTime.getMinutes());
    const seconds = addZero(currentTime.getSeconds());

    // Định dạng thời gian theo 'YYYY-MM-DD HH:mm:ss'
    const formattedTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

    

    const postData = {
        userId1: userId1,
        userId2: userId2,
        time: formattedTime,
    };


    fetch('message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': userTokenValue, // Thay thế your_access_token_here bằng token của bạn
        },
        body: JSON.stringify(postData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        // Xử lý dữ liệu trả về từ API
        console.log(data);
        // Gọi hàm xử lý dữ liệu ở đây nếu cần
        processData(data,userId2);
    })
    .catch(error => {
        // Xử lý lỗi nếu có
        console.error('There was a problem with the fetch operation:', error);
    });
}

