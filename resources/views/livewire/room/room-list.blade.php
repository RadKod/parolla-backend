<div wire:poll.10000ms>
    @push('styles')
    <style>
        .room-card {
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: none;
            height: 100%;
        }
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        .room-card .card-body {
            padding: 1.25rem;
        }
        .room-header {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 0.75rem;
            color: #333;
        }
        .room-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        .room-lang {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            background-color: #e9ecef;
            color: #495057;
            margin-right: 0.5rem;
        }
        .room-count {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .room-action-btn {
            border-radius: 0.35rem;
            font-size: 0.85rem;
            padding: 0.35rem 0.75rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
        }
        .room-filter-bar {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }
        .room-filter-bar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #007bff, #6610f2, #6f42c1);
        }
        .filter-header {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.75rem;
            font-size: 1rem;
            border-bottom: 1px dashed rgba(0,0,0,0.1);
            padding-bottom: 0.5rem;
        }
        .filter-item {
            padding: 0;
            border: none;
        }
        .filter-item select, .filter-item input {
            border-radius: 0.35rem;
            border: 1px solid #ced4da;
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .filter-item select:focus, .filter-item input:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .input-group-text {
            border-radius: 0 0.35rem 0.35rem 0;
            transition: all 0.2s;
        }
        .input-group:hover .input-group-text {
            background-color: #f8f9fa;
        }
        .stats-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 0.85rem;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 0.5rem;
            margin-right: 0.75rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        }
        .stats-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        }
        .stats-badge i {
            font-size: 1.1rem;
            margin-right: 0.5rem;
        }
        .badge-blue {
            background-color: #e6f2ff;
            color: #0a58ca;
            border: 1px solid #cfe2ff;
        }
        .badge-green {
            background-color: #e7f5e9;
            color: #146c43;
            border: 1px solid #d1e7dd;
        }
        .badge-purple {
            background-color: #eee6ff;
            color: #6f42c1;
            border: 1px solid #e2d9f3;
        }
        .badge-rating {
            padding: 0.25rem 0.5rem;
            font-size: 0.85rem;
            background-color: #0d6efd;
            color: white;
            border-radius: 1rem;
        }
        .badge-reviews {
            padding: 0.25rem 0.5rem;
            font-size: 0.85rem;
            background-color: #198754;
            color: white;
            border-radius: 1rem;
        }
        .room-type-toggle {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
            margin-right: 0.5rem;
        }
        .room-type-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 20px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #0d6efd;
        }
        input:checked + .slider:before {
            transform: translateX(20px);
        }
        .room-meta-info {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        .view-count {
            display: inline-flex;
            align-items: center;
            font-size: 0.9rem;
            color: #495057;
            font-weight: 600;
        }
        .api-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            background-color: #6c757d;
            color: white;
            border-radius: 0.25rem;
            margin-right: 0.25rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .api-badge:hover {
            background-color: #5a6268;
            color: white;
        }
        .action-section {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0,0,0,.05);
        }
        .modal-content {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .modal-header {
            border-bottom: 1px solid rgba(0,0,0,.05);
            padding: 1rem 1.5rem;
        }
        .modal-body {
            padding: 1.5rem;
        }
        .modal-footer {
            border-top: 1px solid rgba(0,0,0,.05);
            padding: 1rem 1.5rem;
        }
        .question-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .question-item {
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
            margin-bottom: 1rem;
        }
        .question-item:last-child {
            margin-bottom: 0;
        }
        .question-character {
            font-weight: 600;
            color: #0d6efd;
            margin-right: 0.5rem;
        }
        .question-text {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .answer-text {
            color: #6c757d;
        }
        .review-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .review-item {
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
            margin-bottom: 1rem;
            display: flex;
            flex-direction: column;
        }
        .review-user {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .review-rating {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            background-color: #0d6efd;
            color: white;
            border-radius: 1rem;
            font-size: 0.75rem;
            margin-right: 0.5rem;
        }
        .review-content {
            color: #6c757d;
        }
        .pagination-container {
            margin-top: 1.5rem;
        }
        .pagination {
            justify-content: center;
        }
        .form-label-height {
            height: 21px;
            display: block;
        }
        .form-control-sm {
            height: 38px;
        }
        .btn-sm {
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .controls-row {
            min-height: 38px;
            display: flex;
            align-items: center;
        }
    </style>
    @endpush

    <!-- Filters and Stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="room-filter-bar">
                <div class="filter-header">
                    <i class="ri-filter-3-line mr-2"></i>Oda Listeleme ve Filtreleme
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="d-flex flex-wrap">
                            <span class="stats-badge badge-blue">
                                <i class="ri-door-open-line"></i> Toplam: {{ $publicRoomCount + $privateRoomCount }}
                            </span>
                            <span class="stats-badge badge-green">
                                <i class="ri-globe-line"></i> Açık: {{ $publicRoomCount }}
                            </span>
                            <span class="stats-badge badge-purple">
                                <i class="ri-lock-line"></i> Özel: {{ $privateRoomCount }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-3 mb-2 mb-md-0">
                                <div class="form-group mb-0">
                                    <label class="small text-muted mb-1 form-label-height">Dil Filtresi</label>
                                    <select wire:model="selectedLang" class="form-control form-control-sm">
                                        <option value="all">Dil: Tümü</option>
                                        @foreach($langs as $lang)
                                            <option value="{{ $lang }}">
                                                {{ $lang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0">
                                <div class="form-group mb-0">
                                    <label class="small text-muted mb-1 form-label-height">Oda Tipi</label>
                                    <select wire:model="roomType" class="form-control form-control-sm">
                                        <option value="all">Tümü</option>
                                        <option value="1">Açık</option>
                                        <option value="0">Özel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0">
                                <div class="form-group mb-0">
                                    <label class="small text-muted mb-1 form-label-height">Sıralama</label>
                                    <select wire:model="sortField" class="form-control form-control-sm">
                                        <option value="created_at">Eklenme Tarihi</option>
                                        <option value="reviews_avg">Değerlendirme Skoru</option>
                                        <option value="view_count">Görüntülenme</option>
                                        <option value="question_count">Soru Sayısı</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="small text-muted mb-1 form-label-height">Arama</label>
                                    <div class="input-group">
                                        <input type="text" wire:model="searchTerm" class="form-control form-control-sm" placeholder="Oda ara...">
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-white" style="height: 38px; display: flex; align-items: center;">
                                                <i class="ri-search-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="controls-row">
                            <div class="sort-direction">
                                <button wire:click="sortBy('{{ $sortField }}')" class="btn btn-sm btn-light">
                                    <i class="ri-{{ $sortDirection == 'asc' ? 'sort-asc' : 'sort-desc' }}"></i>
                                    {{ $sortDirection == 'asc' ? 'Artan' : 'Azalan' }}
                                </button>
                            </div>
                            <div class="results-info text-muted small ml-auto">
                                {{ $customQuestions->total() }} sonuçtan {{ $customQuestions->firstItem() ?? 0 }}-{{ $customQuestions->lastItem() ?? 0 }} arası gösteriliyor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Room Cards -->
    <div class="row">
        @foreach($customQuestions as $customQuestion)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card room-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <label class="room-type-toggle mb-0">
                                    <input type="checkbox" wire:click.prevent="changeRoomType('{{$customQuestion->room}}')" 
                                        {{ $customQuestion->is_public ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                                <span class="{{ $customQuestion->is_public ? 'text-primary' : 'text-secondary' }}">
                                    {{ $customQuestion->is_public ? 'Açık' : 'Özel' }}
                                </span>
                            </div>
                            <div>
                                <span class="badge-rating">{{ number_format($customQuestion->reviews->avg('rating') ?? 0, 1) }}</span>
                                <span class="badge-reviews">{{ $customQuestion->reviews->count() }} yorum</span>
                            </div>
                        </div>

                        <h5 class="room-title">
                            <span class="room-lang">{{ $customQuestion->lang }}</span>
                            <a href="https://www.parolla.app/room?id={{$customQuestion->room}}" 
                               data-toggle="tooltip" data-html="true" 
                               title="@foreach($customQuestion->qa_list as $qa_item){{ $qa_item['question'] }}: {{ $qa_item['answer'] }} <br>@endforeach" 
                               target="_blank" class="text-decoration-none">
                                {{ $customQuestion->title }}
                            </a>
                        </h5>

                        <div class="room-meta-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>{{ count($customQuestion->qa_list) }} Soru</span>
                                <span>{{ $customQuestion->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap">
                            <button class="btn btn-primary btn-sm room-action-btn"
                                    wire:click.prevent="selectRoom('{{$customQuestion->room}}')"
                                    data-toggle="modal" data-target="#showDetail">
                                <i class="ri-question-line mr-1"></i> Soruları Gör
                            </button>
                            
                            @if(count($customQuestion->reviews))
                                <button class="btn btn-success btn-sm room-action-btn"
                                        wire:click.prevent="selectRoom('{{$customQuestion->room}}')"
                                        data-toggle="modal" data-target="#showReviews">
                                    <i class="ri-star-line mr-1"></i> Yorumları Gör
                                </button>
                            @endif
                            
                            @if($customQuestion->device_info)
                                <button class="btn btn-info btn-sm room-action-btn"
                                        wire:click.prevent="selectRoom('{{$customQuestion->room}}')"
                                        data-toggle="modal" data-target="#showDeviceInfo">
                                    <i class="ri-device-line mr-1"></i> Cihaz Bilgisi
                                </button>
                            @endif
                        </div>

                        <div class="action-section">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{route('api.modes.custom_get')}}?room={{$customQuestion->room}}"
                                       target="_blank" class="api-badge">
                                        <i class="ri-code-line mr-1"></i> API
                                    </a>
                                    <a href="{{route('api.reviews', $customQuestion->id)}}?room={{$customQuestion->room}}"
                                       target="_blank" class="api-badge">
                                        <i class="ri-star-line mr-1"></i> Reviews API
                                    </a>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="view-count mr-3">
                                        <i class="ri-eye-line mr-1"></i>
                                        {{ number_format($customQuestion->view_count, 0, ',', '.') }}
                                    </div>
                                    <button class="btn btn-danger btn-sm"
                                            wire:click.prevent="deleteRoom('{{$customQuestion->room}}')"
                                            onclick="confirm('Silmek istediğinize emin misiniz?') || event.stopImmediatePropagation()">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="row">
        <div class="col-12 pagination-container">
            {{ $customQuestions->links() }}
        </div>
    </div>

    <!-- Questions Modal -->
    <div wire:ignore.self class="modal fade" id="showDetail" tabindex="-1" role="dialog"
         aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">
                        <i class="ri-question-answer-line mr-2"></i>
                        {{ $selectedRoom ? $selectedRoom->title : '' }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($selectedRoom)
                        <ul class="question-list">
                            @foreach($selectedRoom->qa_list as $qa_item)
                                <li class="question-item">
                                    <div class="question-text">
                                        <span class="question-character">({{$qa_item['character']}})</span>
                                        {{ $qa_item['question'] }}
                                    </div>
                                    <div class="answer-text">{{ $qa_item['answer'] }}</div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click.prevent="closeRoom()" class="btn btn-secondary"
                            data-dismiss="modal">
                        Kapat
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Device Info Modal -->
    <div wire:ignore.self class="modal fade" id="showDeviceInfo" tabindex="-1" role="dialog"
         aria-labelledby="deviceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deviceModalLabel">
                        <i class="ri-mobile-line mr-2"></i>
                        Cihaz Bilgisi
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($selectedRoom)
                        <pre style="background-color: #f4f4f4; padding: 15px; border-radius: 5px; overflow: auto;"><code>{{ json_encode(json_decode($selectedRoom->device_info), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click.prevent="closeRoom()" class="btn btn-secondary"
                            data-dismiss="modal">
                        Kapat
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Modal -->
    <div wire:ignore.self class="modal fade" id="showReviews" tabindex="-1" role="dialog"
         aria-labelledby="reviewsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewsModalLabel">
                        <i class="ri-star-line mr-2"></i>
                        Yorumlar
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($selectedRoom)
                        <ul class="review-list">
                            @foreach($selectedRoom->reviews as $review)
                                <li class="review-item">
                                    <div class="review-user">
                                        <i class="ri-user-line mr-1"></i>
                                        {{ $review->user->username ?? 'Misafir' }}
                                    </div>
                                    <div>
                                        <span class="review-rating">{{ $review->rating }} ★</span>
                                        <span class="review-content">{{ $review->content }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click.prevent="closeRoom()" class="btn btn-secondary"
                            data-dismiss="modal">
                        Kapat
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
