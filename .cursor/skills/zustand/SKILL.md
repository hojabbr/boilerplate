---
name: zustand-state-management
description: 'Introduces and teaches how to use Zustand for state management in React applications. Activates when creating stores, updating shared state, reading state in components, managing complex state logic, wiring state changes to UI, building interactive apps (e.g., games like Tic‑Tac‑Toe), and when the user mentions global state, store, hooks, selectors, middleware, or state update patterns.'
license: MIT
metadata:
    author: pmndrs
---

# Zustand State Management (Intro + Tic‑Tac‑Toe Tutorial)

## When to Apply

Activate this skill when:

- You need global or shared state in a React app
- Browsing state across multiple components
- Updating and reading state from anywhere
- Optimising re‑renders with selectors
- Building interactive UIs (e.g., games, UI logic)
- Maintaining state outside local component state

## Documentation

Use `search-docs` to open the Zustand **Getting Started** section for fundamentals or the **Tic‑Tac‑Toe tutorial** to learn by building a real example.  
Zustand is a lightweight, minimal state management library with a simple hook‑based API and no providers. [oai_citation:1‡zustand.docs.pmnd.rs](https://zustand.docs.pmnd.rs/getting-started/introduction)

---

## Introduction

Zustand (German for “state”) is a small, fast, and scalable state management solution based on hooks and a simple API. You can create a store containing state and actions, and use that store from any component without a Provider. [oai_citation:2‡zustand.docs.pmnd.rs](https://zustand.docs.pmnd.rs/getting-started/introduction)

### Why Zustand?

- Very little boilerplate
- Works with React out of the box
- No provider component required
- Can manage any JS state — primitives, objects, arrays, functions
- Good performance via selectors
- Extensible via middleware such as persistence or devtools [oai_citation:3‡zustand.site](https://zustand.site/en/docs)

---

## Basic Setup

### Install

```bash
npm install zustand
```

Create a Store

import { create } from 'zustand'

const useStore = create((set) => ({
count: 0,
increase: () => set((state) => ({ count: state.count + 1 })),
reset: () => set({ count: 0 }),
}))

Use this hook anywhere—no Provider required:

function Counter() {
const count = useStore((state) => state.count)
const increase = useStore((state) => state.increase)

return <button onClick={increase}>{count}</button>
}

This selects only the count and increase parts of the store. Renders are scoped so only components using the selected state change. ￼

⸻

Advanced Concepts

Selectors

Instead of selecting the whole store, pick only the state/actions you need to prevent unnecessary re‑renders:

const bears = useStore((state) => state.bears)

Middleware

Extend functionality using built‑in middleware such as:
• persist to save state to localStorage
• combine to structure complex stores
• devtools to inspect store changes ￼

⸻

Tutorial: Tic‑Tac‑Toe (Hands‑On)

Below is a condensed walkthrough of the Zustand Tic‑Tac‑Toe tutorial, which teaches React + Zustand in a real interactive scenario. ￼

What You’ll Build

An interactive tic‑tac‑toe game with:
• Global state for the board and current player
• Actions to update state
• UI components reacting to state changes
• Game logic such as win detection and turn history ￼

⸻

1. Game Store

Create store with state for history, currentMove, and player turn:

import { create } from 'zustand'
import { combine } from 'zustand/middleware'

const useGameStore = create(
combine(
{ history: [Array(9).fill(null)], currentMove: 0 },
(set) => ({
setHistory: (next) => set((state) => ({ history: typeof next === 'function' ? next(state.history) : next })),
setCurrentMove: (next) => set((state) => ({ currentMove: typeof next === 'function' ? next(state.currentMove) : next })),
}),
),
)

    •	history stores past game boards
    •	currentMove tracks which board is active  ￼

⸻

2. Game Logic Helpers

Create pure functions to calculate game state:

function calculateWinner(squares) {
const lines = [
[0,1,2],[3,4,5],[6,7,8],
[0,3,6],[1,4,7],[2,5,8],
[0,4,8],[2,4,6],
]
for (let [a, b, c] of lines) {
if (squares[a] && squares[a] === squares[b] && squares[a] === squares[c]) {
return squares[a]
}
}
return null
}

function calculateTurns(squares) {
return squares.filter((s) => !s).length
}

function calculateStatus(winner, turns, player) {
if (!winner && !turns) return 'Draw'
if (winner) return `Winner: ${winner}`
return `Next: ${player}`
}

These helpers compute the game outcome. ￼

⸻

3. Controlled Board Component

In your Board component, read current squares and call callbacks on play:

function Board({ squares, xIsNext, onPlay }) {
return (

<div style={{ display: 'grid', gridTemplate: 'repeat(3,1fr) / repeat(3,1fr)' }}>
{squares.map((value, i) => (
<Square
key={i}
value={value}
onSquareClick={() => onPlay(i)}
/>
))}
</div>
)
}

The onPlay function updates state and handles game logic. ￼

⸻

4. History & Time Travel

Use history to allow navigating between past states:

const history = useGameStore((state) => state.history)
const currentMove = useGameStore((state) => state.currentMove)

function jumpTo(move) {
setCurrentMove(move)
}

Render buttons to let players jump to past moves. ￼

⸻

5. Putting It All Together

When a square is clicked:
• Derive the current squares from history[currentMove]
• Merge the new move into history up to the current point
• Update currentMove accordingly

This approach gives you full control over board state and supports time travel. ￼

⸻

Tips & Best Practices
• Always treat state objects/arrays as immutable — return new copies rather than mutating in place.
• Select only needed state slices to minimise UI re‑renders.
• Use middleware to split stores, persist state, or enable devtools.
• Keep stores composable with patterns like combine. ￼

⸻

Summary

Concept Purpose
Store Central state container managed by Zustand
Selectors Read specific parts of the state
Middleware Enhance store (persist, devtools, etc.)
Immutable updates Ensure predictable state transitions
Tutorial (Tic‑Tac‑Toe) Real example showing global state, actions, game logic, and navigation

---
