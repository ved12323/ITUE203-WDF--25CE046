
"use strict";

document.addEventListener("DOMContentLoaded", function () {
    // Load all four JSON files.
    async function loadJSONData() {
        try {
            const [events, notices, faqs] = await Promise.all([
                
                fetch("data/events.json"),
                fetch("data/notices.json"),
                fetch("data/faqs.json")
            ]);

            // Detect missing files or incorrect paths.
            if ( !events.ok ||
                !notices.ok || !faqs.ok) {
                throw new Error(
                    "Could not load one or more JSON files."
                );
            }

         
            const eventData = await events.json();
            const noticeData = await notices.json();
            const faqData = await faqs.json();

            // Render JSON data on the homepage.
            
            renderEvents(eventData);
            renderNotices(noticeData);
            renderFAQs(faqData);

        } catch (error) {
            console.error("JSON loading error:", error);
            showLoadingError();
        }
    }

    // Safely create an element containing text.
    function addText(parent, tag, text, className) {
        const element = document.createElement(tag);
        element.textContent = text || "";

        if (className) {
            element.className = className;
        }

        parent.appendChild(element);
        return element;
    }

    
    // 2. Render JSON events with search, category filter, sorting and pagination.
    let allEvents = [];
    let currentPage = 1;
    const pageSize = 6;

    function renderEvents(events) {
        allEvents = Array.isArray(events) ? events : [];
        populateCategoryFilter(allEvents);
        applyEventControls();

        const searchInput = document.getElementById("event-search");
        const categorySelect = document.getElementById("event-category");
        const sortSelect = document.getElementById("event-sort");
        const previousButton = document.getElementById("event-prev");
        const nextButton = document.getElementById("event-next");

        if (searchInput) {
            searchInput.addEventListener("input", function () {
                currentPage = 1;
                applyEventControls();
            });
        }
        if (categorySelect) {
            categorySelect.addEventListener("change", function () {
                currentPage = 1;
                applyEventControls();
            });
        }
        if (sortSelect) {
            sortSelect.addEventListener("change", function () {
                currentPage = 1;
                applyEventControls();
            });
        }
        if (previousButton) {
            previousButton.addEventListener("click", function () {
                if (currentPage > 1) {
                    currentPage--;
                    applyEventControls();
                }
            });
        }
        if (nextButton) {
            nextButton.addEventListener("click", function () {
                const filtered = getFilteredEvents();
                const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
                if (currentPage < totalPages) {
                    currentPage++;
                    applyEventControls();
                }
            });
        }
    }

    function populateCategoryFilter(events) {
        const select = document.getElementById("event-category");
        if (!select) return;

        const categories = [...new Set(events.map(function (event) {
            return event.category;
        }).filter(Boolean))].sort();

        // Keep the "All categories" option and rebuild the rest from JSON.
        select.replaceChildren();
        const allOption = document.createElement("option");
        allOption.value = "all";
        allOption.textContent = "All categories";
        select.appendChild(allOption);

        categories.forEach(function (category) {
            const option = document.createElement("option");
            option.value = category;
            option.textContent = category;
            select.appendChild(option);
        });
    }

    function getFilteredEvents() {
        const searchValue = (document.getElementById("event-search")?.value || "")
            .trim().toLowerCase();
        const categoryValue = document.getElementById("event-category")?.value || "all";
        const sortValue = document.getElementById("event-sort")?.value || "date-asc";

        const filtered = allEvents.filter(function (event) {
            const searchableText = [
                event.title, event.category, event.venue, event.organizer,
                event.description, ...(Array.isArray(event.tags) ? event.tags : [])
            ].join(" ").toLowerCase();

            const matchesSearch = searchableText.includes(searchValue);
            const matchesCategory = categoryValue === "all" || event.category === categoryValue;
            return matchesSearch && matchesCategory;
        });

        filtered.sort(function (a, b) {
            if (sortValue === "title-asc") {
                return (a.title || "").localeCompare(b.title || "");
            }
            const dateOrder = (a.date || "").localeCompare(b.date || "");
            return sortValue === "date-desc" ? -dateOrder : dateOrder;
        });

        return filtered;
    }

    function applyEventControls() {
        const container = document.getElementById("events-list");
        if (!container) return;

        const filteredEvents = getFilteredEvents();
        const totalPages = Math.max(1, Math.ceil(filteredEvents.length / pageSize));
        currentPage = Math.min(currentPage, totalPages);
        const startIndex = (currentPage - 1) * pageSize;
        const pageEvents = filteredEvents.slice(startIndex, startIndex + pageSize);

        container.replaceChildren();
        if (pageEvents.length === 0) {
            addText(container, "p", "No events match your search. Try another keyword or category.", "event-empty");
        } else {
            pageEvents.forEach(function (event) {
                const card = document.createElement("article");
                card.className = "event-result-card";

                const topLine = document.createElement("div");
                topLine.className = "event-card-topline";
                addText(topLine, "span", event.category || "Campus event", "event-category-pill");
                if (event.badge) addText(topLine, "span", event.badge, "event-badge");
                card.appendChild(topLine);

                addText(card, "h3", `${event.icon || "📅"} ${event.title}`);
                addText(card, "p", event.description || "More details will be announced soon.", "event-description");

                const details = document.createElement("dl");
                details.className = "event-details";
                [
                    ["Date", event.date || "To be announced"],
                    ["Time", event.time || "To be announced"],
                    ["Venue", event.venue || "To be announced"],
                    ["Organizer", event.organizer || "StudentHub team"],
                    ["Entry", event.fee || "Check with organizer"]
                ].forEach(function (detail) {
                    const wrapper = document.createElement("div");
                    addText(wrapper, "dt", detail[0]);
                    addText(wrapper, "dd", detail[1]);
                    details.appendChild(wrapper);
                });
                card.appendChild(details);
                container.appendChild(card);
            });
        }

        const count = document.getElementById("event-count");
        if (count) count.textContent = `${filteredEvents.length} event${filteredEvents.length === 1 ? "" : "s"} found`;

        const pageStatus = document.getElementById("event-page-status");
        if (pageStatus) pageStatus.textContent = `Page ${currentPage} of ${totalPages}`;

        const previousButton = document.getElementById("event-prev");
        const nextButton = document.getElementById("event-next");
        if (previousButton) previousButton.disabled = currentPage <= 1;
        if (nextButton) nextButton.disabled = currentPage >= totalPages;
    }

    // 3. Display the latest notice in the banner and modal.
    function renderNotices(notices) {
        const bannerText =
            document.getElementById("notice-banner-text");
        const modalTitle =
            document.getElementById("modal-title");
        const modalBody =
            document.getElementById("notice-modal-body");

        if (!notices.length) {
            if (bannerText) bannerText.textContent = "No notices available.";
            if (modalBody) modalBody.textContent = "No notices available.";
            return;
        }

        // Select the first notice in the JSON file.
        const notice = notices[0];

        if (bannerText) {
            bannerText.textContent =
                notice.priority + ": " + notice.title;
        }

        if (modalTitle) {
            modalTitle.textContent = notice.title;
        }

        if (modalBody) {
            modalBody.replaceChildren();

            addText(modalBody, "p",
                "Notice Number: " + notice.noticeNo);
            addText(modalBody, "p",
                "Category: " + notice.category);
            addText(modalBody, "p",
                "Priority: " + notice.priority);
            addText(modalBody, "p",
                "Date: " + notice.date);
            addText(modalBody, "p",
                "Department: " + notice.department);
            addText(modalBody, "p",
                "Issued by: " + notice.issuedBy);
            addText(modalBody, "p", notice.summary);
            addText(modalBody, "p", notice.content);
        }
    }

    // 4. Display FAQs from faqs.json.
    function renderFAQs(faqs) {
        const container = document.getElementById("faq-list");
        if (!container) return;

        container.replaceChildren();

        faqs.forEach(function (faq) {
            const item = document.createElement("div");
            item.className = "faq-item";

            const question = document.createElement("button");
            question.type = "button";
            question.className = "faq-question";
            question.setAttribute("aria-expanded", "false");

            addText(question, "span", faq.question);
            addText(question, "span", "+", "faq-icon");

            const answer = document.createElement("div");
            answer.className = "faq-answer";

            addText(answer, "p", faq.answer);

            item.appendChild(question);
            item.appendChild(answer);
            container.appendChild(item);

            // Keep the existing accordion style and behavior.
            question.addEventListener("click", function () {
                const isOpen = item.classList.contains("active");

                container.querySelectorAll(".faq-item").forEach(
                    function (otherItem) {
                        otherItem.classList.remove("active");

                        const otherButton =
                            otherItem.querySelector(".faq-question");

                        otherButton.setAttribute(
                            "aria-expanded", "false"
                        );

                        otherItem.querySelector(".faq-icon")
                            .textContent = "+";
                    }
                );

                if (!isOpen) {
                    item.classList.add("active");
                    question.setAttribute("aria-expanded", "true");
                    question.querySelector(".faq-icon")
                        .textContent = "−";
                }
            });
        });
    }

    function showLoadingError() {
        [
            "students-list",
            "events-list",
            "faq-list"
        ].forEach(function (id) {
            const element = document.getElementById(id);
            if (element) {
                element.textContent =
                    "Unable to load data. Check the JSON file paths.";
            }
        });

        const banner = document.getElementById("notice-banner-text");
        if (banner) {
            banner.textContent = "Unable to load notices.";
        }

        const body = document.getElementById("notice-modal-body");
        if (body) {
            body.textContent =
                "Notice details could not be loaded. Please check the JSON files.";
        }
    }

    // Start loading data.
    loadJSONData();
});
