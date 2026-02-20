import * as React from "react"

import type * as ChartRecharts from "./chart-recharts"

const chartFallback = (
  <div className="flex aspect-video items-center justify-center text-muted-foreground text-xs">
    Loading chart…
  </div>
)

const ChartContainerLazy = React.lazy(() =>
  import("./chart-recharts").then((m) => ({ default: m.ChartContainer }))
)
const ChartTooltipLazy = React.lazy(() =>
  import("./chart-recharts").then((m) => ({ default: m.ChartTooltip }))
)
const ChartTooltipContentLazy = React.lazy(() =>
  import("./chart-recharts").then((m) => ({ default: m.ChartTooltipContent }))
)
const ChartLegendLazy = React.lazy(() =>
  import("./chart-recharts").then((m) => ({ default: m.ChartLegend }))
)
const ChartLegendContentLazy = React.lazy(() =>
  import("./chart-recharts").then((m) => ({ default: m.ChartLegendContent }))
)
const ChartStyleLazy = React.lazy(() =>
  import("./chart-recharts").then((m) => ({ default: m.ChartStyle }))
)

export type { ChartConfig } from "./chart-recharts"

export function ChartContainer(
  props: React.ComponentProps<typeof ChartRecharts.ChartContainer>
) {
  return (
    <React.Suspense fallback={chartFallback}>
      <ChartContainerLazy {...props} />
    </React.Suspense>
  )
}

export function ChartTooltip(
  props: React.ComponentProps<typeof ChartRecharts.ChartTooltip>
) {
  return (
    <React.Suspense fallback={null}>
      <ChartTooltipLazy {...props} />
    </React.Suspense>
  )
}

export function ChartTooltipContent(
  props: React.ComponentProps<typeof ChartRecharts.ChartTooltipContent>
) {
  return (
    <React.Suspense fallback={null}>
      <ChartTooltipContentLazy {...props} />
    </React.Suspense>
  )
}

export function ChartLegend(
  props: React.ComponentPropsWithoutRef<typeof ChartRecharts.ChartLegend>
) {
  return (
    <React.Suspense fallback={null}>
      <ChartLegendLazy {...props} />
    </React.Suspense>
  )
}

export function ChartLegendContent(
  props: React.ComponentPropsWithoutRef<
    typeof ChartRecharts.ChartLegendContent
  >
) {
  return (
    <React.Suspense fallback={null}>
      <ChartLegendContentLazy {...props} />
    </React.Suspense>
  )
}

export function ChartStyle(
  props: React.ComponentProps<typeof ChartRecharts.ChartStyle>
) {
  return (
    <React.Suspense fallback={null}>
      <ChartStyleLazy {...props} />
    </React.Suspense>
  )
}
