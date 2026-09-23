@props([
    'listId',
    'reorderUrl',
    'payloadKey',
    'enabled' => true,
])

@if($enabled)
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.7/Sortable.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const list = document.getElementById(@json($listId));
                if (! list || typeof Sortable === 'undefined') {
                    return;
                }

                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                const toast = document.getElementById('sortable-toast');
                const payloadKey = @json($payloadKey);
                const reorderUrl = @json($reorderUrl);

                let saving = false;

                const showToast = (message, isError = false) => {
                    if (! toast) {
                        return;
                    }
                    toast.textContent = message;
                    toast.classList.remove('hidden', 'bg-green-50', 'text-green-700', 'border-green-100', 'bg-red-50', 'text-red-700', 'border-red-100');
                    toast.classList.add(isError ? 'bg-red-50' : 'bg-green-50', isError ? 'text-red-700' : 'text-green-700', isError ? 'border-red-100' : 'border-green-100');
                    window.clearTimeout(showToast._timer);
                    showToast._timer = window.setTimeout(() => toast.classList.add('hidden'), 2800);
                };

                const refreshOrderBadges = () => {
                    list.querySelectorAll('[data-sortable-item]').forEach((el, index) => {
                        el.querySelectorAll('[data-order-badge]').forEach(badge => {
                            badge.textContent = index + 1;
                        });
                    });
                };

                Sortable.create(list, {
                    animation: 160,
                    handle: '[data-drag-handle]',
                    draggable: '[data-sortable-item]',
                    ghostClass: 'opacity-60',
                    onEnd: async () => {
                        if (saving) {
                            return;
                        }

                        const ids = [...list.querySelectorAll('[data-sortable-item]')]
                            .map(el => parseInt(el.dataset.id, 10))
                            .filter(id => ! Number.isNaN(id));

                        if (ids.length < 2) {
                            return;
                        }

                        saving = true;
                        refreshOrderBadges();

                        try {
                            const response = await fetch(reorderUrl, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrf,
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({ [payloadKey]: ids }),
                            });

                            const data = await response.json().catch(() => ({}));

                            if (! response.ok) {
                                throw new Error(data.message || 'Gagal menyimpan urutan.');
                            }

                            showToast(data.message || 'Urutan berhasil disimpan.');
                        } catch (error) {
                            showToast(error.message || 'Gagal menyimpan urutan.', true);
                        } finally {
                            saving = false;
                        }
                    },
                });
            });
        </script>
    @endpush
@endif
