---
name: ux-ui-automation-fullstack
description: Thiết kế giao diện UX/UI chuyên nghiệp (Next.js, React, Tailwind CSS, Shadcn UI, Dark Mode) và xây dựng kịch bản tự động hóa chuẩn production (Playwright, Cypress, LangGraph AI Agents, n8n, API Workflow) tích hợp sẵn code mẫu thực chiến. Sử dụng khi người dùng yêu cầu tạo, tối ưu, đánh giá giao diện web/app, design system, viết test E2E, hoặc viết script/workflow tự động hóa tác vụ.
---
# UX UI Automation Fullstack

Kỹ năng toàn diện (All-in-One) tích hợp cả quy chuẩn thiết kế giao diện (UX/UI), kiến trúc tự động hóa (E2E Browser Automation, Workflow & Multi-Agent Automation) và các mẫu code thực chiến (Playbooks) trong một file duy nhất, tương thích trực tiếp với mọi nền tảng AI Agent.

## When to Use

- Khi thiết kế mới, làm đẹp, hoặc refactor giao diện web/mobile (React, Next.js App Router, Tailwind CSS, Shadcn UI, HTML/CSS).
- Khi xây dựng Design System, tối ưu Dark Mode, phân cấp chữ (typography), hoặc kiểm tra khả năng truy cập (Accessibility / WCAG).
- Khi viết kịch bản tự động hóa trình duyệt, kiểm thử giao diện E2E (Playwright, Cypress, Puppeteer, Selenium).
- Khi xây dựng workflow tự động hóa quy trình, tích hợp API, hoặc điều phối Multi-Agent (Node.js/TypeScript, Python, LangGraph, n8n, CI/CD).
- Khi người dùng phàn nàn giao diện trông "giống AI tạo đại trà" hoặc script tự động hóa hay bị lỗi ngẫu nhiên (flaky tests).

## Steps

### 1. Phân tích ngữ cảnh và mục tiêu trước khi viết code

Xác định rõ đầu vào và ràng buộc kỹ thuật trước khi sinh mã nguồn:

1. **Đối với tác vụ UX/UI**: Xác định đối tượng người dùng mục tiêu, loại thiết bị chính (Desktop-first hay Mobile-first), tech stack hiện tại (Next.js, Tailwind CSS, Shadcn UI) và chế độ màu (ưu tiên hỗ trợ Dark Mode sắc nét).
2. **Đối với tác vụ Automation**: Xác định môi trường chạy (Headless browser, CI/CD container hay máy local), cơ chế xác thực (Session, StorageState, Token), và ngưỡng chịu lỗi (Timeout, Rate limit, Human-in-the-loop).

### 2. Thiết kế hệ thống UX/UI chuẩn Production (Design System & Layout)

Áp dụng hệ thống quy chuẩn định lượng thay vì chọn thông số ngẫu nhiên để giao diện có chiều sâu và đồng nhất:

- **Hệ thống khoảng cách (8pt Grid System)**: Chỉ sử dụng các bội số của 4px hoặc 8px cho `padding`, `margin` và `gap` (`4px`, `8px`, `12px`, `16px`, `24px`, `32px`, `48px`, `64px`). Giữ khoảng cách bên trong phần tử (padding) nhỏ hơn khoảng cách giữa các khối độc lập (margin/gap) để tạo nhóm thị giác rõ ràng (Law of Proximity).
- **Phân cấp chữ (Typography Hierarchy)**: Giới hạn tối đa 2 font families (1 Sans-serif cho UI như Inter/Geist, 1 Monospace cho Code/Data như JetBrains Mono/Geist Mono).
  - Tiêu đề chính (H1): `28px - 36px` (`text-3xl` đến `text-4xl`), `font-bold`, `tracking-tight` (`-0.02em`).
  - Tiêu đề phụ (H2/H3): `18px - 24px` (`text-lg` đến `text-2xl`), `font-semibold`, `leading-snug`.
  - Nội dung chính (Body): `14px - 16px` (`text-sm` đến `text-base`), `font-normal`, `leading-relaxed`, độ dài dòng tối ưu `60ch - 75ch`.
  - Số liệu & Bảng biểu: Luôn bật `tabular-nums` để các chữ số thẳng hàng dọc khi cập nhật thời gian thực.
- **Công thức phối màu 60-30-10 & Chuẩn Dark Mode**:
  - **60% Màu nền (Background/Surface)**: Tránh dùng `#000000` hoặc `#FFFFFF` thuần túy trên toàn mặt phẳng. Dùng Slate/Zinc sâu cho Dark Mode (`#09090B` hoặc `#0B0F19`) và nền phân tách nhẹ cho Light Mode (`#F8FAFC`).
  - **30% Màu cấu trúc (Card/Elevated Surface/Border)**: Trong Dark Mode, phân tách độ cao (elevation) bằng độ sáng nền tăng nhẹ (`#121726` hoặc `bg-zinc-900/80`) kết hợp viền mảnh tinh tế (`border-white/10` hoặc `border-zinc-800`) thay vì dùng bóng đổ đen.
  - **10% Màu nhấn (Primary Accent & Semantic)**: Chỉ dùng màu nổi bật cho hành động chính (Primary CTA). Đảm bảo độ tương phản chữ/nền đạt chuẩn WCAG AA (tối thiểu `4.5:1` cho chữ thường).

### 3. Quy chuẩn chuyên sâu cho Next.js, Tailwind CSS & Shadcn UI

Khi triển khai giao diện với bộ công cụ web hiện đại, áp dụng các quy tắc sau:

- **Kiến trúc Component (Next.js App Router)**:
  - Mặc định dùng **React Server Components (RSC)** cho các trang lấy dữ liệu và khung bố cục tĩnh; chỉ đặt `'use client'` ở các lá (leaf components) thực sự cần `useState`, `useEffect` hoặc bắt sự kiện trình duyệt.
  - Bọc các vùng tải dữ liệu chậm bằng `<Suspense fallback={<Skeleton />}>` để trang phản hồi tức thì mà không chặn toàn bộ màn hình.
- **Chuẩn hóa Tailwind CSS & Shadcn UI**:
  - Sử dụng biến màu ngữ nghĩa (`bg-background`, `text-foreground`, `bg-card`, `border-border`, `text-muted-foreground`) thay vì gán mã màu cứng để tự động tương thích cả Dark/Light Mode.
  - Dùng hàm tiện ích `cn()` (`clsx` + `tailwind-merge`) cho mọi component tái sử dụng để cho phép ghi đè class an toàn.
  - Tối ưu Form với `react-hook-form` kết hợp `zod` để kiểm tra dữ liệu đầu vào theo thời gian thực và hiển thị lỗi ngay bên dưới trường nhập (`<FormMessage />`).

### 4. Hoàn thiện 5 trạng thái tương tác của mọi Component (UI State Matrix)

Mỗi màn hình hoặc component có dữ liệu động đều phải được thiết kế đủ 5 trạng thái:

1. **Empty State (Trạng thái rỗng)**: Hiển thị biểu tượng minh họa nhẹ nhàng, tiêu đề rõ ràng, mô tả ngắn gọn và nút CTA tạo dữ liệu đầu tiên.
2. **Loading State (Trạng thái đang tải)**: Sử dụng Skeleton Loader khớp kích thước thực tế (chống giật bố cục - CLS = 0) và khóa nút bấm kèm spinner khi đang gửi biểu mẫu (`disabled={isPending}`).
3. **Partial / Ideal State (Trạng thái chuẩn)**: Xử lý chuỗi dài bằng `truncate` hoặc `line-clamp-2`, cố định chiều cao ảnh bằng `aspect-ratio`.
4. **Error State (Trạng thái lỗi)**: Hiển thị thông báo lỗi cụ thể kèm nút "Thử lại" (Retry) hoặc hướng dẫn khắc phục rõ ràng.
5. **Interactive Micro-states**: Trang bị đầy đủ `hover:`, `focus-visible:ring-2`, `active:scale-[0.98]`, và `transition-all duration-150 ease-out` cho mọi nút và thẻ tương tác.

### 5. Xây dựng kịch bản UI Automation & Playwright chống lỗi (Zero-Flake Pattern)

Khi viết code điều khiển trình duyệt hoặc kiểm thử E2E (Playwright, Cypress):

- **Mô hình Page Object Model (POM) & Resilient Locators**:
  - Tách biệt bộ chọn phần tử (Locators) và hành động (Actions) vào các class Page Object, không viết locator rời rạc trong file test.
  - Thứ tự ưu tiên chọn phần tử: `getByRole()` -> `getByLabel()` -> `getByPlaceholder()` -> `getByTestId()`. Chủ động gắn `data-testid="module-element-action"` lên các phần tử UI phức tạp.
  - Tuyệt đối không dùng XPath tuyệt đối hoặc class Tailwind/CSS để làm selector kiểm thử.
- **Web-First Assertions & Network Control**:
  - Thay thế hoàn toàn `waitForTimeout()` bằng `await expect(locator).toBeVisible()` hoặc `await page.waitForResponse()`.
  - Tái sử dụng trạng thái đăng nhập bằng `storageState` (`auth.json`) để bỏ qua bước đăng nhập lặp lại ở từng kịch bản.
  - Cấu hình `trace: 'on-first-retry'` và `screenshot: 'only-on-failure'` để tự động ghi lại toàn bộ lịch sử DOM, console log và network khi xảy ra lỗi.

### 6. Xây dựng Workflow, n8n & AI Agent Automation bền bỉ

Khi thiết kế luồng tự động hóa tác vụ ngầm, tích hợp API, n8n hoặc Multi-Agent (LangGraph):

1. **Tính lũy đẳng (Idempotency) & Quản lý trạng thái (Checkpointing)**:
   - Mọi bước thực thi phải có `idempotency_key` hoặc kiểm tra trạng thái tồn tại trước khi ghi vào cơ sở dữ liệu để chạy lại nhiều lần không gây trùng lặp.
   - Với luồng AI Agent (LangGraph), định nghĩa State Schema tường minh bằng TypeScript/Zod hoặc Pydantic và lưu checkpoint sau mỗi node.
2. **Cơ chế thử lại (Retry with Exponential Backoff) & Dead-Letter Queue**:
   - Tự động thử lại tối đa 3-5 lần với thời gian chờ tăng dần (`1s -> 2s -> 4s + jitter`) cho lỗi mạng hoặc `HTTP 429 / 502 / 503 / 504`.
   - Đẩy các bản ghi lỗi vượt ngưỡng sang nhánh xử lý lỗi riêng (Error Workflow / Dead-Letter Queue) kèm thông báo cảnh báo thay vì làm sập toàn bộ tiến trình.
3. **Human-in-the-Loop (HITL) & Dry-Run**:
   - Bắt buộc chèn bước tạm dừng chờ phê duyệt (Approval Gate) hoặc hỗ trợ cờ `--dry-run` trước các hành động nhạy cảm (xóa dữ liệu, thanh toán, gửi email hàng loạt, đẩy code lên production).

### 7. Mẫu Code Thực Chiến (Production Playbooks)

#### 7.1. Hệ màu chuẩn Dark Mode (Tailwind CSS Variables)

```css
@layer base {
  :root {
    --background: 210 40% 98%;      /* #F8FAFC - Nền sáng dịu */
    --foreground: 222.2 84% 4.9%;   /* #020817 - Chữ chính sắc nét */
    --card: 0 0% 100%;              /* #FFFFFF - Thẻ nổi */
    --border: 214.3 31.8% 91.4%;    /* #E2E8F0 - Viền mảnh */
    --primary: 221.2 83.2% 53.3%;   /* #2563EB - Điểm nhấn chính */
  }
  .dark {
    --background: 222 47% 5%;       /* #070A12 - Nền tối sâu chuẩn Developer */
    --foreground: 210 40% 98%;      /* #F8FAFC - Chữ sáng rõ */
    --card: 222 47% 8%;             /* #0B101E - Bề mặt Card phân lớp */
    --border: 217.2 32.6% 15%;      /* Viền tinh tế không gây chói */
    --primary: 217.2 91.2% 59.8%;   /* #3B82F6 - Màu nhấn đạt chuẩn WCAG */
  }
}
```

#### 7.2. Mẫu React Component đủ 5 trạng thái (UI State Matrix + Testability)

```tsx
"use client";
import * as React from "react";
import { AlertCircle, Inbox } from "lucide-react";

interface DataListProps<T> {
  items: T[];
  isLoading: boolean;
  error?: string | null;
  onRetry?: () => void;
  onCreateNew?: () => void;
  renderItem: (item: T) => React.ReactNode;
}

export function ProductionDataList<T>({
  items,
  isLoading,
  error,
  onRetry,
  onCreateNew,
  renderItem,
}: DataListProps<T>) {
  if (isLoading) {
    return (
      <div data-testid="data-list-loading" aria-busy="true" className="space-y-3">
        {Array.from({ length: 4 }).map((_, idx) => (
          <div key={idx} className="h-16 w-full animate-pulse rounded-lg border border-zinc-800/60 bg-zinc-900/40 p-4" />
        ))}
      </div>
    );
  }

  if (error) {
    return (
      <div role="alert" data-testid="data-list-error" className="flex flex-col items-center justify-center rounded-xl border border-red-500/30 bg-red-500/10 p-6 text-center">
        <AlertCircle className="mb-2 h-8 w-8 text-red-400"/>
        <p className="text-sm font-medium text-red-200">{error}</p>
        {onRetry && (
          <button type="button" data-testid="data-list-retry-btn" onClick={onRetry} className="mt-4 inline-flex min-h-[44px] items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-400">
            Thử tải lại
          </button>
        )}
      </div>
    );
  }

  if (items.length === 0) {
    return (
      <div data-testid="data-list-empty" className="flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-800 bg-zinc-950/50 p-10 text-center">
        <Inbox className="mb-3 h-10 w-10 text-zinc-500"/>
        <h3 className="text-base font-semibold text-zinc-200">Chưa có dữ liệu nào</h3>
        <p className="mt-1 max-w-sm text-sm text-zinc-400">Bắt đầu bằng cách khởi tạo bản ghi đầu tiên cho hệ thống của bạn.</p>
        {onCreateNew && (
          <button type="button" data-testid="data-list-create-btn" onClick={onCreateNew} className="mt-4 inline-flex min-h-[44px] items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-500 active:scale-[0.98]">
            Tạo mới ngay
          </button>
        )}
      </div>
    );
  }

  return (
    <div data-testid="data-list-container" className="divide-y divide-zinc-800/80 rounded-xl border border-zinc-800 bg-zinc-900/50">
      {items.map(renderItem)}
    </div>
  );
}
```

#### 7.3. Mẫu Playwright Page Object Model (Zero-Flake Pattern)

```typescript
import { type Page, type Locator, expect } from '@playwright/test';

export class DashboardPage {
  readonly page: Page;
  readonly createButton: Locator;
  readonly dataListContainer: Locator;

  constructor(page: Page) {
    this.page = page;
    this.createButton = page.getByRole('button', { name: /tạo mới ngay/i });
    this.dataListContainer = page.getByTestId('data-list-container');
  }

  async createItemAndWaitForApi(itemName: string) {
    const responsePromise = this.page.waitForResponse(
      (res) => res.url().includes('/api/items') && res.status() === 201
    );
    await this.createButton.click();
    await this.page.getByLabel('Tên mục').fill(itemName);
    await this.page.getByRole('button', { name: 'Lưu' }).click();
    await responsePromise;
    await expect(this.dataListContainer).toContainText(itemName);
  }
}
```

#### 7.4. Mẫu LangGraph State & Hàm Retry Exponential Backoff

```typescript
import { Annotation } from "@langchain/langgraph";

export const AutomationAgentState = Annotation.Root({
  taskId: Annotation<string>(),
  retryCount: Annotation<number>({ reducer: (curr, upd) => curr + upd, default: () => 0 }),
  requiresHumanApproval: Annotation<boolean>({ reducer: (_, upd) => upd, default: () => false }),
  status: Annotation<"pending" | "running" | "awaiting_approval" | "completed" | "failed">(),
});

export async function withRetry<T>(fn: () => Promise<T>, maxRetries = 3, baseDelayMs = 1000): Promise<T> {
  let lastError: unknown;
  for (let attempt = 0; attempt <= maxRetries; attempt++) {
    try {
      return await fn();
    } catch (error: any) {
      lastError = error;
      const status = error?.status || error?.response?.status;
      if (status && [400, 401, 403, 404].includes(status)) throw error;
      if (attempt === maxRetries) break;
      const delay = baseDelayMs * Math.pow(2, attempt) + Math.random() * 250;
      await new Promise((r) => setTimeout(r, delay));
    }
  }
  throw lastError;
}
```

### 8. Tự kiểm tra chất lượng trước khi bàn giao (Verification Checklist)

- **Kiểm tra Giao diện (UX/UI)**:
  - Đã kiểm tra bố cục trên cả 3 kích thước màn hình (`375px` Mobile, `768px` Tablet, `1280px+` Desktop) và chế độ Dark Mode chưa?
  - Các nút chỉ có icon đã có `aria-label` và vùng chạm tối thiểu `44x44px` trên di động chưa?
- **Kiểm tra Tự động hóa (Automation)**:
  - Đã xóa toàn bộ lệnh `sleep` cứng và chuyển thông tin nhạy cảm (API Key, Token, Password) sang biến môi trường `.env` chưa?
  - Đã đảm bảo đóng browser/context hoặc giải phóng kết nối trong khối `finally` / `test.afterEach` chưa?

## Gotchas

- **Bẫy giao diện "AI Slop"**: Tránh lạm dụng dải màu tím-xanh lòe loẹt, tránh bo góc quá đà không đồng nhất, và tránh đặt quá nhiều Card lồng nhau. Hãy ưu tiên nền tối sâu (`#0B0F19`), viền mảnh `1px` tinh tế và căn lề trái sắc nét.
- **Lỗi Hydration Mismatch trong Next.js**: Không dùng `window`, `localStorage`, `Math.random()` hoặc `new Date()` trực tiếp trong quá trình render lần đầu của Component mà chưa đưa vào `useEffect` hoặc bọc Client Only.
- **Quên khóa nút khi gọi bất đồng bộ**: Không khóa nút Submit ngay khi nhấn sẽ khiến người dùng hoặc script tự động kích hoạt 2-3 request trùng nhau.
- **Vòng lặp vô tận trong AI Agent / Workflow**: Khi xây dựng luồng tự động hóa có vòng lặp tự sửa lỗi (Self-reflection / Retry loop), luôn phải đặt `max_iterations` hoặc `recursionLimit` (ví dụ tối đa 5-10 vòng) để tránh treo tiến trình và đốt cạn ngân sách API.