import * as path from 'path';

const PROJECT_CONFIG = {
  projectName: 's360-toolkit-layout-builder',
  pkg: require(path.resolve('package.json')),
  entryPoints: ['s360-toolkit-layout-builder.admin.js']
};

export { PROJECT_CONFIG };
