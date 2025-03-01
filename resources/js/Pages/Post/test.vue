<template>
    <div>
<a @click="test">GO</a>
        <div v-if="data">
            <div v-for="(item, itemIndex) in data" :key="item.id" class="questionnaire-container">
                <div class="mt-2 mb-4">
                    {{ item.start_at }}
                    <span class="text-lg font-bold">{{ item.name }}</span>
                </div>

                <div v-if="item.questions && item.questions.length > 0">
                    <div v-for="(question, questionIndex) in item.questions" :key="question.id">
                        <h3 class="text-sm font-medium mt-8 mb-1">{{ question.name }}</h3>

                        <textarea
                            readonly
                            rows="2"
                            class="bg-gray-200 border-gray-200 rounded w-full text-gray-700">{{ question.description }}</textarea>

                        <div v-if="question.answers && question.answers.length > 0" class="mb-2">
                            <div v-for="answer in question.answers" :key="answer.id" class="answer-option mb-2">
                                <label class="flex items-center space-x-2">
                                    <input
                                        type="radio"
                                        :value="answer"
                                        v-model="selectedAnswers[question.id]"
                                    />
                                    <span>{{ answer.name }}</span>
                                </label>
                            </div>
                        </div>


                        <!-- Поля для комментария и загрузки файла -->
                        <div class="mt-4" v-if="selectedAnswers[question.id]">
            <textarea
                v-model="selectedAnswers[question.id]['scomment']"
                placeholder="Добавьте комментарий (необязательно)"
                class="w-full p-2 border rounded-md"
            ></textarea>
                            <input
                                type="file"
                                multiple
                                @change="handleFileUpload($event, question.id)"
                                class="mt-2"
                            />
                        </div>
                    </div>
                </div>

            </div>

            <button
                @click="submit"
                class="mt-4 bg-blue-500 text-white py-2 px-4 rounded-md disabled:bg-gray-400"
            style="margin-left: 300px; margin-bottom: 300px">
                Отправить
            </button>


        </div>

    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';


const selectedAnswers = ref({
    id: null, name: null, comment: null, score: null, file: null
}); // Инициализация
const selectedFiles = ref({}); // Инициализация
const comments = ref([]); // Инициализация

const data = ref([]);

const uploadedFiles = ref<File[]>([]);
const uploadedFiless = ref([]);
const uploadedFile = ref<File | null>(null); // Загруженный файл

let myObject = {}; // Убедитесь, что это определено


const files = ref([])

// Обработка загрузки файла
const handleFileUpload = (event: Event, id) => {

   //  selectedFiles[id] = event.target.files[0]
  //  console.log(selectedFiles)
    // const selectedFiles = event.target.files;
    // if (selectedFiles.length > 0) {
    //     // Добавляем выбранные файлы в массив files
    //     this.files.push(...Array.from(selectedFiles));
    // }


    const fileInput = event.target as HTMLInputElement;
    if (fileInput.files && fileInput.files.length > 0) {
        uploadedFile.value = fileInput.files[0];
    }


        files.value[id] = {
            file: uploadedFile.value || null
    };


}



onMounted(async () => {
    try {
        const response = await axios.get('/test/getquestions')
        data.value = response.data; // Предполагается, что данные находятся в response.data

    } catch (error) {
        console.error('Ошибка при получении данных:', error);
    }
});




function submit(){
const res = [];

    data.value.forEach(item => {

        const temp = []
        item.questions.forEach(question => {
           temp[question.id] = selectedAnswers.value[question.id]
        })

        res[item.name] = temp

    });


    const formData = new FormData();
    uploadedFiles.value.forEach(file => {
        formData.append('files', JSON.stringify(res)); // Добавляем каждый файл в FormData
        formData.append('answers', res); // Добавляем каждый файл в FormData
    });
console.log(formData)


    axios.post('/test/send', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        }
    )
        .then(response => {
            console.log('Ответы успешно отправлены:', response.data);
        })
        .catch(error => {
            console.error('Ошибка при отправке ответов:', error);
        });

}
</script>


<style scoped>



.questionnaire-container {
    margin-top: 1.5rem;
    max-width: 600px;
    padding: 1.5rem;
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    margin-left: 300px;
}
.answer-option label {
    cursor: pointer;
}
textarea {
    resize: vertical;
}
button {
    cursor: pointer;
}
</style>
