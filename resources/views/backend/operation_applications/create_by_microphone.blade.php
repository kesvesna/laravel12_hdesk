@extends('layouts.backend.main')

@section('title', 'Главная | Создание заявки в эксплуатацию')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Заявка через микрофон</h4>
                    </div>
                </div>
            </div>
            <div class="profile-foreground position-relative"
                 style="
                        margin-top: -1.5rem !important;
                        margin-right: -1.5rem !important;
                        margin-left: -1.5rem !important;
                     ">
                <div class="profile-wid-bg">
                    <img src="{{asset('assets/images/profile-bg.jpg')}}" alt="" class="profile-wid-img" />
                </div>

                <div class="pt-4 mb-lg-3 pb-lg-4 px-4">
                    <div class="row">
                        <div class="col">
                            <form action="{{route('operation_applications.store')}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    @include('components.backend.selects.create.trk_id')
                                    @include('components.backend.selects.create.user_division_id')
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="trouble_description" class="form-label form-label-sm">Проблема <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <textarea rows="5" required name="trouble_description" class="form-control form-control-sm"
                                                      placeholder="Нажмите Начать запись и начните говорить" id="trouble_description">{{old('trouble_description')}}</textarea>
                                            @error('trouble_description')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="button-group d-flex flex-wrap gap-2">
                                        <button type="button" id="toggleRecord" class="btn btn-primary btn-sm flex-grow-1 flex-md-grow-0">
                                            Начать запись 🎤
                                        </button>
                                        <button type="button" id="resetText" class="btn btn-warning btn-sm flex-grow-1 flex-md-grow-0">
                                            Сброс
                                        </button>
                                        <button type="submit" id="submitBtn" class="btn btn-success btn-sm flex-grow-1 flex-md-grow-0">Отправить</button>
                                    </div>
                                    <div class="input-group input-group-sm mt-3">
                                        <a href="{{route('operation_applications.index')}}"
                                           class="btn btn-sm btn-outline-success col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/skip-backward.svg')}}"
                                                alt="back" title="Назад"></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Modal info -->
        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel2">Выбор подразделения</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Проблемы с климатом (жарко, холодно, не работает вентиляция) - ХВО</p>
                        <p>Проблемы с автоматикой - АСУ</p>
                        <p>Проблемы с видеонаблюдением, доступом в помещения - ТСО</p>
                        <p>Любые проблемы - Служба эксплуатации ТРК</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

            if (!SpeechRecognition) {
                alert('Ваш браузер не поддерживает распознавание речи. Пожалуйста, используйте Chrome или Edge.');
            } else {
                const recognition = new SpeechRecognition();
                recognition.continuous = true;
                recognition.interimResults = true; // Включаем промежуточные результаты для плавного вывода
                recognition.lang = 'ru-RU';

                const toggleBtn = document.getElementById('toggleRecord');
                const resetBtn = document.getElementById('resetText');
                const textarea = document.getElementById('trouble_description');
                let isRecording = false;
                let interimTranscript = '';

                recognition.onresult = (event) => {
                    let finalTranscript = '';
                    interimTranscript = '';

                    for (let i = event.resultIndex; i < event.results.length; i++) {
                        const transcript = event.results[i][0].transcript;
                        if (event.results[i].isFinal) {
                            finalTranscript += transcript;
                        } else {
                            interimTranscript += transcript;
                        }
                    }

                    // Добавляем финальный текст в textarea, не удаляя предыдущий
                    if (finalTranscript) {
                        textarea.value += finalTranscript;
                        scrollTextareaToBottom();
                    }

                    // Отображаем промежуточный текст временно, не стирая основной текст
                    // Для удобства можно показать промежуточный текст в конце (например, в скобках)
                    // Но чтобы не затирать основной текст, мы не трогаем textarea.value, а можем
                    // например, показать interim в placeholder или отдельном элементе.
                    // Здесь для простоты игнорируем interim отображение в textarea.
                };

                recognition.onerror = (event) => {
                    console.error('Ошибка распознавания:', event.error);
                    stopRecording();
                };

                recognition.onend = () => {
                    if (isRecording) {
                        // Автоматически перезапускаем распознавание, чтобы запись шла непрерывно
                        recognition.start();
                    } else {
                        stopRecording();
                    }
                };

                toggleBtn.addEventListener('click', () => {
                    if (!isRecording) {
                        startRecording();
                    } else {
                        stopRecording();
                    }
                });

                resetBtn.addEventListener('click', () => {
                    textarea.value = '';
                });

                function startRecording() {
                    recognition.start();
                    isRecording = true;
                    toggleBtn.textContent = 'Остановить запись ⏹';
                    toggleBtn.classList.remove('btn-primary');
                    toggleBtn.classList.add('btn-danger');
                }

                function stopRecording() {
                    recognition.stop();
                    isRecording = false;
                    toggleBtn.textContent = 'Начать запись 🎤';
                    toggleBtn.classList.remove('btn-danger');
                    toggleBtn.classList.add('btn-primary');
                }

                function scrollTextareaToBottom() {
                    // Автопрокрутка textarea вниз по мере добавления текста
                    textarea.scrollTop = textarea.scrollHeight;
                }
            }
        </script>
@endsection
