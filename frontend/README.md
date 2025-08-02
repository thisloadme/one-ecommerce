# Dota 2 Captain Mode Draft Simulator

A Dota 2 draft simulation application that allows users to practice drafting strategies against AI-powered bots.

## Main Features

- **Complete Draft Simulation**: Follows Captain Mode format with proper ban and pick order
- **Team Selection**: Choose between Radiant or Dire team
- **AI Bot**: Face against bot that uses AI to make ban and pick decisions
- **Interactive Interface**: Responsive UI with animations and visual effects
- **Hero Search**: Search heroes by typing hero names
- **Time Limit**: Optional time limit for each draft phase (1-300 seconds)
- **Draft Analysis**: Get in-depth analysis about the draft after completion, including:
  - Win probability for each team
  - Draft strengths and weaknesses
  - Factors affecting match outcomes
  - Strategy suggestions for both teams

## Technologies

- Nuxt 3
- Vue 3
- Pinia
- Tailwind CSS
- Google Gemini AI

## How to Use

1. Choose team (Radiant or Dire)
2. (Optional) Enable time limit and set duration (1-300 seconds)
3. Wait for countdown to finish
4. During your turn:
   - Click hero to select
   - Press "Ban Hero" or "Pick Hero" button
   - If time limit is enabled, you must select within the specified time
5. During bot's turn:
   - Bot will "think" for 1-3 seconds
   - Bot will select heroes automatically
6. After draft is complete:
   - Click "Get Analysis" button
   - Wait for AI analysis results
   - Read in-depth analysis about the draft

## Controls

- **Mouse**: 
  - Click hero to select
  - "Ban Hero": To ban the selected hero
  - "Pick Hero": To pick the selected hero
  - "Get Analysis": To get draft analysis (appears after draft is complete)
  - "Reset Simulation": To restart draft with the same team
  - "Restart Simulation": To return to team selection
- **Keyboard**: Type hero name to search
- **Time Limit**:
  - Timer will appear during your turn
  - Button will flash red when time runs out
  - Timer is only active during your turn

## Installation

```bash
# Clone repository
git clone [repository-url]

# Install dependencies
npm install

# Setup environment variables
cp .env.example .env
# Edit .env and add GEMINI_API_KEY

# Run development server
npm run dev
```

## Environment Variables

```env
GEMINI_API_KEY=your_gemini_api_key_here
```

## Build for Production

```bash
npm run build
npm run preview
```

## License

[GNU GPL v3](https://choosealicense.com/licenses/gpl-3.0/)
