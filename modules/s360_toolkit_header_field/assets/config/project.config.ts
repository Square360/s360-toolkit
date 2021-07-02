import * as path from 'path';

const PROJECT_CONFIG = {
  projectName: 's360-toolkit-header-field',
  pkg: require(path.resolve('package.json')),
  entryPoints: ['s360-toolkit-header-field.admin.js']
};

export { PROJECT_CONFIG };
