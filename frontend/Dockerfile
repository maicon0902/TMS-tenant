FROM node:20-alpine

WORKDIR /app

COPY package*.json ./

# Install dependencies (will be overwritten by volume, but needed for initial setup)
RUN npm install || true

COPY . .

EXPOSE 3000

# Use a script that ensures dependencies are installed before starting
# Wait for volume mount, check if node_modules exists, install if needed, then start dev server
# Use --force flag to ensure optional dependencies (like rollup native modules) are installed
CMD ["sh", "-c", "sleep 3 && if [ ! -d node_modules ] || [ ! -f node_modules/.package-lock.json ]; then npm install --force; fi && npm run dev -- --host 0.0.0.0 --port 3000"]

